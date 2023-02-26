<?php

namespace Core\Framework\Router;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension Twig permettant d'appeler des fonctions definie du Routeur a l'interieur des vues twig
 */

class RouterTwigExtension extends AbstractExtension
{
    private Router $router;

    /**
     * On Recupere l'instance du routeur et l'enregistre
     */

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Declare les fonctions disponible coté vue 
     * @return TwigFunction[]
     */

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'path'])
        ];
    }

    /**
     * Fait appel a la methode generateUri() du routeur et retourne son resultat
     */

    public function path(string $name, array $params = []): string
    {
        return $this->router->generateUri($name, $params);
    }
}
