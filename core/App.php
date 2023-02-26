<?php

// Objet de construction avec notre container de dependances et toutes nos depedances 

namespace Core;

use Core\Framework\Middleware\MiddlewareInterface;
use Core\Framework\Router\Router;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;

// A pour charge de charger toutes les parties du site 
class App
{

    private Router $router;

    private array $modules;

    private ContainerInterface $container;

    private MiddlewareInterface $middleware;

    // Initialise la liste des modules et enregistre le container de dependance

    public function __construct(ContainerInterface $container, array $modules = [])
    {

        //On recup le routeur
        $this->router = $container->get(Router::class);

        // On initialise nos dependances 

        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }

        $this->container = $container;
    }

    // Methode pour lancer l'application et traite la requete du serveur en l'envoyant dans la chaine de responsabilité

    public function run(ServerRequestInterface $request): ResponseInterface  // Permet de démarrer la chaine de vérif 
    {
        return $this->middleware->process($request); // Lance la chaine de responsabilités / Les middlewares
    }

    // Link le tout premier middleware de notre chaine de responsabilité

    public function linkFirst(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->middleware = $middleware;
        return $middleware;
    }

    /**
     * Retourne l'instance de PHP DI
     * @return ContainerInterface
     */

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
