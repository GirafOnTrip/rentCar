<?php

namespace App\Car\Action;

use Model\Entity\Marque;
use Core\Toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class CarAction

{

    use RedirectTrait;

    private Router $router;

    private ContainerInterface $container;

    private RendererInterface $renderer; // Ici c'est la déclaration 

    private EntityManager $manager; // Ici c'est la déclaration  

    private Toaster $toaster; // Permet d'afficher des messages d'erreur ou succès 

    private $marqueRepository; // Pour récuperer quelque chose qui est deja dans la bdd on utilise Repository 

    private $repository; // Pour récuperer quelque chose qui est deja dans la bdd on utilise Repository 

    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster, ContainerInterface $container, Router $router) // Ici j'injecte mes dépendances dans les parenthèses
    {

        $this->renderer = $renderer; // Renderer permet d'afficher le rendu de la page 
        $this->manager = $manager; // Manager permet de sauvegarder des données en BDD
        $this->toaster = $toaster; // Ici l'assignation 

        //Sert a manipuler les marques en base de données
        $this->marqueRepository = $manager->getRepository(Marque::class); // Récupère le Repository de l'objet Marque

        //Sert a manipuler les vehicules en base de données
        $this->repository = $manager->getRepository(Vehicule::class); // Permet de récupérer les infos de la classe Véhicule
        $this->container = $container;
        $this->router = $router;
    }



    public function addCar(ServerRequestInterface $request)
    {

        // Recupere la methode utilisé pour la requete (POST ou GET)
        $method = $request->getMethod();

        //Si le formulaire à été soumis
        if ($method === 'POST') {

            //On recupere le contenu de $_POST (les valeurs saisies dans le formulaire)
            $data = $request->getParsedBody();
            //On recupere le contenu de $_FILES a l'index "image" (Les fichiers chargés dans le formulaire, avec un input de type file')
            $file = $request->getUploadedFiles()["image"];
            //On instancie le Validator en lui passant le tableau de données à valider
            $validator = new Validator($data);
            //On fixe les régles a respecter sur chaque input du formulaire, si il y en a, et on recupere les erreurs ou null
            $errors = $validator
                ->required('modele', 'couleur', 'marque')
                ->getErrors();
            //SI il y a des erreurs, on cree un Toast par erreur et on redirige l'utilisateur afin d'afficher les messages
            if ($errors) {
                //Boucle sur le tableau d'erreurs
                foreach ($errors as $error) {
                    //Création du Toast
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR);
                }
                //Redirection
                return (new Response())
                    ->withHeader('Location', '/admin/addCar');
            }


            // On verifie que l'image soit conforme (voir commentaire de la methode)
            $error = $this->fileGuards($file);

            //Si on a des erreurs on return le Toast (Le Toast a été Génerer par 'fileGuard')
            if ($error !== true) {

                return $error;
            }

            // Si tout va bien avec le fichier, on recupere le nom 

            $fileName = $file->getClientFileName();

            //On assemble le nom du fichier avec le chemin du dossier ou il sera enregistré
            $imgPath = $this->container->get('img.basePath') . $fileName;
            //On tente de le déplacer au chenin voulu
            $file->moveTo($imgPath);

            //Si le déplacement n'est pas possible on créer un Toast et on redirige
            if (!$file->isMoved()) {
                $this->toaster->makeToast("Une erreur s'est produite durant l'enregistrement, merci de réessayer !", Toaster::ERROR);

                // return (new Response())
                //     ->withHeader('Location', '/admin/addCar');

                return $this->redirect('car.add');
            }


            //Si tout s'est bien passe2e on créer un nouveau véhicule 
            $new = new Vehicule(); // Ici j'instancie un nouveau véhicule contenu dans la variable $new 
            // On recuepere l'objet qui represente la marque choisie
            $marque = $this->marqueRepository->find($data['marque']); // Pour récuperer quelque chose qui est deja dans la bdd on utilise Repository 
            //Si on a bien reussi a recuperer une marque, on complete les infos du vehicule puis on l'enregistre
            if ($marque) {

                //Completion des infos du véhicule
                $new->setModel($data['modele'])
                    ->setMarque($marque)
                    ->setCouleur($data['couleur'])
                    ->setImgPath($fileName);

                //Preparation a l'enregistrement en base de données
                $this->manager->persist($new); // Se prépare a enregistrer l'instance en BDD
                $this->manager->flush(); // Enregistre les données en BDD

                $this->toaster->makeToast('Véhicule ajouter avec succès', Toaster::SUCCESS); // Crée le Toast en cas Succès 
            }
            // return (new Response)
            //     ->withHeader('Location', '/admin/listCar');

            //Dans tous les cas on fini par rediriger
            return $this->redirect('car.list');
        }


        // On recuepere les marques
        $marques = $this->marqueRepository->findAll();

        // Retourne la listes des vehicules en bdd
        return $this->renderer->render('@car/addCar', [
            'marques' => $marques
        ]);
    }

    public function update(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id'); // Permet de récupérer l'ID de la voiture 
        $voiture = $this->repository->find($id); // Permet de récupérer la voiture en fonction de son ID
        $method = $request->getMethod(); // Permet de savoir quel méthode de formulaire à étais utiliser ( On la recupere)

        // Est ce que l'on a soumis le formulaire

        if ($method === 'POST') {

            // getParsedBody() permet de récuperer les données $_POST
            $data = $request->getParsedBody();

            //On recupere les fichiers chargés si il y en a, sinon un tableau vide
            $file = $request->getUploadedFiles();

            $validator = new Validator($data);

            $error = $validator
                ->required('modele', 'couleur', 'marque')
                ->getErrors();

            //On verifie si un fichier a ete chargé et qu'il n'y a pas eu d'erreur de chargement
            if (sizeof($file) > 0 && $file['image']->getError() !== 4) {

                // On recupere le nom de l'ancienne image du vehicule
                $oldImg = $voiture->getImgPath();
                // On recupere toutes les informations de la nouvelle image
                $newImg = $file['image'];
                // On recupeere le nom de la nouvelle image
                $imgName = $newImg->getClientFileName();
                // On joint le nom de l'image au chemin du dossier ou l'on souhaite l'enregistrer
                $imgPath = $this->container->get('img.basePath') . $imgName;
                // On verifie la nouvelle image
                $this->fileGuards($newImg);
                //Si il y a une erreur avec le fichier, on retourne l'erreur

                if ($error) {
                    return $error;
                }

                //On tente de la deplacer
                $newImg->moveTo($imgPath);


                //SI l'image a bien ete deplace
                if ($newImg->isMoved()) {

                    // On lie la nouvelle image avec le vehicuke
                    $voiture->setImgPath($imgName);
                    // On supprime l'ancienne du server avec la fonction unlink
                    $oldPath = $this->container->get('img.basePath') . $oldImg;

                    unlink($oldPath);
                }
            }




            // FIN TEST

            //On recupere la marque choisie
            $marque = $this->marqueRepository->find($data['marque']); // permet d'aller récupérer l'id de marque

            $voiture->setModel($data['modele']) // Ici je modifie les données de la voiture en question ainsi que sa marque
                ->setMarque($marque)
                ->setCouleur($data['couleur']);


            //TEST ENVOI OU NON DU FORMULAIRE

            $this->manager->flush(); // Enregistre les données en BDD
            $this->toaster->makeToast('Mise à jour réussi !', Toaster::SUCCESS); // Crée le Toast en cas Succès 

            // return (new Response)
            //     ->withHeader('Location', '/admin/listCar'); // Redirige vers la page listCar une fois le formulaire valider

            //On redirige sur la liste des vehicules
            return $this->redirect('car.list');
        }

        //Si le formulaire n'a pas ete soumis

        // On recupere les marques en base de données pour les utiliser dans le menu select de la vue
        $marques = $this->marqueRepository->findAll();

        // On retourne la vue avec le véhicule que l'on souhaite modifier et la liste des marques
        return $this->renderer->render('@car/updateCar', [  // Si jamais on ne rentre pas dans la condition affiche la page update
            "voiture" => $voiture,
            'marques' => $marques
        ]);
    }

    /**
     * Supprime un véhicule de la bdd
     *
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\MessageInterface
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */

    public function delete(ServerRequestInterface $request): Response
    {

        $id = $request->getAttribute('id'); // Permet de récupérer l'ID de la voiture 
        $voiture = $this->repository->find($id); // Permet de récupérer la voiture en fonction de son ID

        $this->manager->remove($voiture); // Ici je supprime la voiture sélectionner
        $this->manager->flush(); // Enregistre les données en BDD (On le fais a l'aide de Doctrine)

        // On recupere le nom de l'ancienne image du vehicule
        $oldImg = $voiture->getImgPath();

        // On supprime l'ancienne du server avec la fonction unlink
        $oldPath = $this->container->get('img.basePath') . $oldImg;
        unlink($oldPath);


        $this->toaster->makeToast('Véhicule supprimer avec succès', Toaster::SUCCESS); // Crée le Toast en cas Succès 

        // return (new Response())
        //     ->withHeader('Location', '/admin/listCar'); // Redirige vers la page listCar une fois le formulaire valider

        return $this->redirect('car.list');
    }



    public function listCar(ServerRequestInterface $request): string
    {
        $voitures = $this->repository->findAll(); // Permet de récupérer les véhicules en BDD

        return $this->renderer->render('@car/list', [
            "voitures" => $voitures
        ]);
    }

    public function userListCar(ServerRequestInterface $request): string
    {
        $voitures = $this->repository->findAll(); // Permet de récupérer les véhicules en BDD

        return $this->renderer->render('@car/listUser', [
            "voitures" => $voitures
        ]);
    }



    //Documentation

    /**
     * Affiche les données d'un véhicule
     * @param ServerRequestInterface $request
     * @return string | Response
     */


    public function viewCar(ServerRequestInterface $request)
    {

        $id = $request->getAttribute('id'); // Permet de récupérer l'ID de la voiture passez en parametre
        $voiture = $this->repository->find($id); // Permet de récupérer la voiture en fonction de son ID


        // Si pas de vehicule retourne une erreur
        if (!$voiture) {

            return new Response(404, [], 'Aucun vehicule ne correspond');
        }

        // ON REND LA VUE EN PASSANT EN PARAMETRE LE VEHICULE
        return $this->renderer->render('@car/viewCar', [ // Redirige vers la page viewCar de la voiture en question 
            "voiture" => $voiture
        ]);
    }


    public function userViewCar(ServerRequestInterface $request)
    {

        $id = $request->getAttribute('id'); // Permet de récupérer l'ID de la voiture passez en parametre
        $voiture = $this->repository->find($id); // Permet de récupérer la voiture en fonction de son ID


        // Si pas de vehicule retourne une erreur
        if (!$voiture) {

            return new Response(404, [], 'Aucun vehicule ne correspond');
        }

        // ON REND LA VUE EN PASSANT EN PARAMETRE LE VEHICULE
        return $this->renderer->render('@car/userViewCar', [ // Redirige vers la page userViewCar de la voiture en question 
            "voiture" => $voiture
        ]);
    }



    /**
     * Check si une image est conforme au restrictions du serveur
     * @param UploadedFile $file
     * @return MessageInterface|true
     */

    private function fileGuards(UploadedFile $file)
    {
        // Handle Server error
        //Destructuration de tableau
        // S'assure qu'il n'y a pas eu d'erreur au chargement de l'image
        if ($file->getError() === 4) {

            $this->toaster->makeToast("Une erreur est survenu lors du chargement du fichier.", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addCar');
        }

        //list permet de decomposer le contenu d'un tableau afin d'en extraire les valeurs et de les stocker dand des variables
        // On recupere le type et le format du fichier
        list($type, $format) = explode('/', $file->getClientMediaType()); // getClientMediaType renvoi le type MIME d'un fichier
        // exemple de type MIME : image/jpg

        // Handle format error
        //On verifie que le format et le type de fichier correspondent aux formats et type autorisé, sinon on renvoi une erreur
        if (!in_array($type, ['image']) or !in_array($format, ['jpg', 'jpeg', 'png'])) {
            $this->toaster->makeToast("ERREUR : Le format du fichier n'est pas valide, merci de charger un .png, .jpeg ou .jpg", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addCar');
        }

        // Handle Excessive size
        // On verifie que la taille du fichier en octets ne dépasse pas les 2Mo
        if ($file->getSize() > 22047674) {
            $this->toaster->makeToast("Merci de choisir un fichier n'excédant pas 2Mo", Toaster::ERROR);
            return (new Response())
                ->withHeader('Location', '/admin/addCar');
        }

        return true;
    }
}
