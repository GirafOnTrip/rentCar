<?php

namespace Core\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;

/**
 * Si la requete arrive ici une erreur 404 est emise
 * Il est possible de rediriger vers une page (Faire une page 404 stylisé)
 * declarer un renderer pour recup le routeur
 */

class NotFoundMiddleware extends AbstractMiddleware
{
    public function process(ServerRequestInterface $request)
    {
        return new Response(404, [], "Page introuvable Erreur 404");
    }
}
