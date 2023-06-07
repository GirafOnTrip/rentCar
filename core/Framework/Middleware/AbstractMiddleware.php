<?php

// L'espace de noms de cette classe est "Core\Framework\Middleware"
namespace Core\Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface; // On importe l'interface ServerRequestInterface pour la gestion des requêtes HTTP
use Core\Framework\Middleware\MiddlewareInterface; // On importe l'interface MiddlewareInterface

// On définit la classe AbstractMiddleware qui implémente l'interface MiddlewareInterface
abstract class AbstractMiddleware implements MiddlewareInterface
{

    protected MiddlewareInterface $next; // On définit une propriété $next qui contiendra le Middleware suivant

    // La méthode linkWith permet de lier le Middleware courant avec le Middleware suivant et retourne ce dernier
    public function linkWith(MiddlewareInterface $middleware): MiddlewareInterface
    {
        $this->next = $middleware;
        return $middleware;
    }

    // La méthode process permet de gérer le Middleware courant
    public function process(ServerRequestInterface $request)
    {
        // On appelle la méthode process du Middleware suivant
        return $this->next->process($request);
    }
}