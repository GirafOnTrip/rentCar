<?php

namespace Core\Framework\TwigExtensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/** Extension Twig permettant d'acceder directement au dossier public
 * Utile pour donner les chemins des feuilles de style, des scripts js, 
 * des images et de tout ce qui peut se trouver dans le dossier assets de public
 */

class AssetsTwigExtension extends AbstractExtension 
{

    public function getFunctions()
    {
        return [
            new TwigFunction('assets', [$this, 'asset'])
        ];
    }

    public function asset(string $path): string
    {
        $file = dirname(__DIR__,3) . '/public/' . $path;
        if(!file_exists($file)) {
            throw new \Exception("Le fichier $file n'existe pas.");
        }
        
        $path .= '?' . filemtime($file);
        return $path;
    }
}