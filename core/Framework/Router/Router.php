<?php

namespace Core\Framework\Router;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;
use Core\Framework\Router\Route;

class Router
{
    private FastRouteRouter $router;

    private array $routes = [];

    /**
     * Instancie un FastRouteRouter et l'enregistre
     */

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**Ajoute une route disponible en methode get */

    public function get(string $path, $callable, string $name): void // $callable est égal à la fonction à appeler 
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
        $this->routes[] = $name;
    }

     /**Ajoute une route disponible en methode post */

    public function post(string $path, $callable, string $name = null): void // $callable est égal à la fonction à appeler 
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }

    // On verifie que l'URL correspond  et la methode requete correspond a une route connue
    // SI oui, retourne un objet Route qui correspond

    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);

        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        } else {
            return null;
        }
    }

    // Les commentaires ainsi permete de bien documenter et de retrouver mes commentaires quand je passe la souris sur les methodes

    // Genere les differentes URI de la route demandee en fonction de son nom ( Les fins d'URLs )
    // [Optionnel] : On peut ajouter un tableau de parametre 
    /**
     * @param string $name
     */

    public function generateUri(string $name, ?array $params = []): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}

// Ceci est une classe en langage PHP appelée Router.
// Elle fait partie du namespace Core\Framework\Router. Cette classe est utilisée pour créer et gérer les routes dans une application.
// Elle utilise la classe FastRouteRouter du namespace Zend\Expressive\Router, la classe ServerRequestInterface du namespace Psr\Http\Message
// et la classe Route du namespace Zend\Expressive\Router.

// La classe possède deux propriétés privées :

// $router: une instance de la classe FastRouteRouter qui est responsable de la gestion du routage de l'application.
// $routes: un tableau qui stocke les noms des routes créées par la classe.
// La classe possède deux méthodes publiques:

// get(): prend en entrée un chemin, une fonction de rappel et un nom en tant que paramètres, crée une nouvelle route avec la méthode GET et le chemin,
// la fonction de rappel et le nom donnés, et l'ajoute au routeur et au tableau $routes.
// match(): prend en entrée une ServerRequestInterface $request en tant que paramètre, utilise le routeur pour faire correspondre la demande à une route,
// et renvoie le résultat de la correspondance. Si la correspondance n'est pas réussie, il renvoie null.

// Cette classe permet de créer et gérer facilement les routes dans une application en utilisant la bibliothèque FastRouteRouter,
// ce qui facilite la gestion des demandes HTTP et la redirection vers la fonction de rappel appropriée.