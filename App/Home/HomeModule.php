<?php

namespace App\Home;

use Core\Framework\AbstractClass\AbstractModule;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\Router;

class HomeModule extends AbstractModule

{

    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    private Router $router;

    private RendererInterface $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->router = $router;
        $this->renderer = $renderer;

        $this->renderer->addPath('home', __DIR__ . DIRECTORY_SEPARATOR . 'view');
        $this->router->get('/', [$this, 'index'], 'accueil');
    }

    public function index()
    {
        return $this->renderer->render('@home/index',
    ['siteName' => 'RentCar']);
    }
}

// Cet extrait de code fait partie d'une application PHP utilisant des concepts tels que l'injection de dépendances et l'utilisation de modèles de conception tels que le pattern MVC.
// Il définit une classe appelée HomeModule qui appartient au namespace App\Home.

// La classe a deux propriétés privées, $router et $renderer, qui sont injectées lors de la création d'une instance de cette classe.
// Ces propriétés sont des objets de la classe Router et RendererInterface respectivement.

// Dans le constructeur de la classe, une méthode addPath() est appelée sur l'objet $renderer pour ajouter un chemin d'accès aux vues du module.
// Ensuite, un itinéraire est ajouté à l'objet $router en utilisant la méthode get().
// Cet itinéraire est défini pour le chemin racine '/', et la méthode index() de cette classe est spécifiée comme la fonction de rappel à utiliser lorsque cet itinéraire est appelé.
// La méthode index() utilise l'objet $renderer pour rendre la vue '@home/index'.