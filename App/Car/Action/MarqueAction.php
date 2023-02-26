<?php

namespace App\Car\Action;

use Model\Entity\Marque;
use Core\Toaster\Toaster;
use Model\Entity\Vehicule;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;

class MarqueAction
{
    private RendererInterface $renderer; // Ici c'est la déclaration 

    private EntityManager $manager; // Manager permet de sauvegarder des données en BDD 

    private Toaster $toaster; // Ici c'est la déclaration 

    private $marqueRepository; // // Pour récuperer quelque chose qui est deja dans la bdd on utilise Repository 

    private $repository;

    public function __construct(RendererInterface $renderer, EntityManager $manager, Toaster $toaster) // Ici j'injecte mes dépendances dans les parenthèses
    {
        $this->renderer = $renderer; // Renderer permet d'afficher le rendu de la page 
        $this->manager = $manager; // Manager permet de sauvegarder des données en BDD
        $this->toaster = $toaster; // Ici l'assignation 
        $this->marqueRepository = $manager->getRepository(Marque::class); // Récupère le Repository de l'objet Marque
        $this->repository = $manager->getRepository(Vehicule::class); // Permet de récupérer les infos de la classe Véhicule
    }

    /**
     * Methode ajoute un vehicule en bdd
     * @param ServerRequestInterface $request
     * @return MessageInterface|string
     */

    public function addMarque(ServerRequestInterface $request)
    {
        $method = $request->getMethod();

        if ($method === 'POST') {
            $data = $request->getParsedBody();
            $marques = $this->marqueRepository->findAll(); // Ici on récupére le nom de chaque marque dans la BDD
            $validator = new Validator($data);
            $errors = $validator->required("marque")   // Permet de vérifier si le champs "marque" est rempli 
                                ->getErrors();
            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR); // Si le champs "marque" n'est pas rempli affiche une ERREUR
                }
                return (new Response())
                    ->withHeader('Location', '/admin/addMarque');
            }

            foreach ($marques as $marque) {
                if ($marque->getName() === $data['marque']) { // Ici je vérifie si le nom de la marque rentrer n'existe pas dèja en BDD

                    $this->toaster->makeToast('Cette marque existe déjà', Toaster::ERROR); // Créer et affiche le Toast "ERREUR"

                    return $this->renderer->render('@car/addMarque'); // Retourne la page addMarque 
                }
            }

            $new = new Marque();
            $new->setName($data['marque']);
            $this->manager->persist($new);
            $this->manager->flush();
            $this->toaster->makeToast('Marque crée avec succès', Toaster::SUCCESS); // Crée le Toast en cas Succès 

            return (new Response())
                ->withHeader('Location', '/admin/listCar');
        }

        return $this->renderer->render('@car/addMarque');
    }

    public function listMarque(ServerRequestInterface $request)
    {
        $marques = $this->marqueRepository->findAll(); // Permet de récupérer toutes les marques dans le repository

        return $this->renderer->render('@car/listMarque', [ // Redirige vers la page Liste Marque
            'marques' => $marques
        ]);
    }

    // HORS COURS //

    /**
     * Supprime une Marque de la bdd
     *
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\MessageInterface
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteMarque(ServerRequestInterface $request): Response
    {
        $id = $request->getAttribute('id'); // Permet de récupérer l'ID de la marque
        $marque = $this->marqueRepository->find($id); // Permet de récupérer la Marque en fonction de son ID (ne pas oublier d'utiliser marqueRepository)

        $this->manager->remove($marque); // Ici je supprime la marque sélectionner
        $this->manager->flush(); // Enregistre les données en BDD (On le fais a l'aide de Doctrine)

        $this->toaster->makeToast('Marque supprimer avec succès', Toaster::SUCCESS); // Crée le Toast en cas Succès 

        return (new Response())
            ->withHeader('Location', '/admin/listMarque'); // Redirige vers la page listMarque une fois le formulaire valider
    }

    public function updateMarque(ServerRequestInterface $request)
    {
        $id = $request->getAttribute('id'); // Permet de récupérer l'ID de la marque 
        $marque = $this->marqueRepository->find($id); // Permet de récupérer la marque en fonction de son ID

        $method = $request->getMethod(); // Permet de savoir quel méthode de formulaire à étais utiliser 

        if ($method === 'POST') {
            $data = $request->getParsedBody(); // getParsedBody() permet de récuperer les données $_POST

            $validator = new Validator($data);
            $errors = $validator->required('name')
            ->getErrors();
            if ($errors) {
                foreach ($errors as $error) {
                    $this->toaster->makeToast($error->toString(), Toaster::ERROR); // Si le champs "marque" n'est pas rempli affiche une ERREUR
                }
                return (new Response())
                    ->withHeader('Location', '/admin/updateMarque');
            }

            $marque->setName($data['name']); // Ici je modifie la propriete "Name" stocker dans $data  (Voir input de updateMarque.html.twig a l'endroit Name = de l'input)

            $this->manager->flush(); // Enregistre les données en BDD
            $this->toaster->makeToast('Mise à jour réussi !', Toaster::SUCCESS); // Crée le Toast en cas Succès 

            return (new Response)
                ->withHeader('Location', '/admin/listMarque'); // Redirige vers la page listCar une fois le formulaire valider
        }

        return $this->renderer->render('@car/updateMarque', [  // Si jamais on ne rentre pas dans la condition affiche la page update
            'marque' => $marque
        ]);
    }
}
