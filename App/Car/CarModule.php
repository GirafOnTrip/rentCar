<?php

namespace App\Car;


use App\Car\Action\CarAction;
use App\Car\Action\MarqueAction;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;


/**
* @inheritDoc
*/

class CarModule extends AbstractModule
{
    private Router $router;

    private RendererInterface $renderer;

    /**
     * @inheritDoc
     */

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    /**
     * Déclare les routes et les methodes disponibles pour ce module, definie le chemin vers le dossier de vues du module
     * Definie eventuellement des variables global a toutes les vues
     */

    public function __construct(ContainerInterface $container)
    {
        //Router pour declarer les routes
        $this->router = $container->get(Router::class); // Router permet de définir les routes des URL

        //Renderer pour declarer les vues
        $this->renderer = $container->get(RendererInterface::class); // Renderer permet d'afficher le rendu de la page 

        //Ensemble d'action possible
        $carAction = $container->get(CarAction::class);
        $marqueAction = $container->get(MarqueAction::class);

        // Le noms de la route est toujours le dernier parametre renseigner

        // Les routes pour les VEHICULe se trouve ici 
        // Declaration du chemin des vue sous le namespace 'car'
        $this->renderer->addPath('car', __DIR__ . DIRECTORY_SEPARATOR . 'view');

        // Declaration des routes disponibles en method GET
        $this->router->get('/admin/addCar', [$carAction, 'addCar'], 'car.add'); // Attribution du nom de la route 
        $this->router->get('/admin/listCar', [$carAction, 'listCar'], 'car.list'); // Attribution du nom de la route 
        $this->router->get('/viewCar/{id:[\d]+}', [$carAction, 'viewCar'], 'car.view');
        $this->router->get('/admin/updateCar/{id:[\d]+}', [$carAction, 'update'], 'car.update'); // Aucune limite de nbr pour l'id {id:[\d+]}
        $this->router->get('/admin/deleteCar/{id:[\d]+}', [$carAction, 'delete'], 'car.delete'); // Route nécessaire à la fonction DELETE
        // Les routes pour la MARQUE se trouve ici 
        $this->router->get('/admin/addMarque', [$marqueAction, 'addMarque'], 'marque.add');
        $this->router->get('/admin/listMarque', [$marqueAction, 'listmarque'], 'list.marque');
        $this->router->get('/admin/deleteMarque/{id:[\d]+}', [$marqueAction, 'deleteMarque'], 'marque.delete'); // Route nécessaire à la fonction DELETE de Marque
        $this->router->get('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'updateMarque'], 'marque.update'); // Aucune limite de nbr pour l'id {id:[\d+]}

        // Les routes pour USER se trouve ici
        $this->router->get('/user/listCar',[$carAction, 'userListCar'], 'userCar.list');
        $this->router->get('/user/viewcar/{id:[\d]+}', [$carAction, 'userViewCar'], 'userCar.view');

        // Declaration des routes disponibles en method POST
        $this->router->post('/admin/updateMarque/{id:[\d]+}', [$marqueAction, 'updateMarque']); // Permet de Modifier le formulaire de Marque en méthode POST
        $this->router->post('/admin/updateCar/{id:[\d]+}', [$carAction, 'update']); // Permet de Modifier le formulaire car méthode POST
        $this->router->post('/admin/addCar', [$carAction, 'addCar']);
        $this->router->post('/admin/addMarque', [$marqueAction, 'addMarque']);
    }
}
