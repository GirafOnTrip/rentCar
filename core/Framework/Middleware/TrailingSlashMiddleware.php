<?php

namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;

/**
 * Retire le slash a la fin de l'url si il en a un
 */

class TrailingSlashMiddleware extends AbstractMiddleware // Permet de retirer le "/" a la fin d'une URL
{
    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        if (!empty($uri) && $uri[-1] === '/' && $uri != '/') { // A pour but de retirer le / a la fin de l'uri 
            return (new Response())
                ->withStatus(301) // 301 = redirection permanente 
                ->withHeader('Location', substr($uri, 0, -1)); //  Ici on rétire le dernier caractere de la chaine 
        }
        return parent::process($request); // Permet de rappeler le parent pour lancer la chaine 
    }
}
