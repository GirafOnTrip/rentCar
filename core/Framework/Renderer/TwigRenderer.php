<?php

namespace Core\Framework\Renderer;

use FilesystemIterator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface

{
    private $twig;
    private $loader;

    /**
     * S'attend a une instance de FilesystemLoader et d'Environment
     * @param FilesystemLoader $loader Objet qui ressence les chemins vers les differents dossier de vues
     * @param Environment $twig Objet qui enregistre nos extensions et permet de faire communiquer vue et controller
     */

    public function __construct(FilesystemLoader $loader, Environment $twig)
    {
        $this->loader = $loader;
        $this->twig = $twig;
    }

    /**
     * Permet d'enregistrer un chemin vers un ensemble de vues
     * @param string $namaspace Si $path est definie $namaspace represente un alias (raccourcis) du chemin vers les vues, sinon contient simplement le chemin
     * @param string|null $path Si definie contient le chemin vers les vues qui seront enregistrer sous la valeur de $namespace
     */

    public function addPath(string $namespace, ?string $path = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    /**
     * Afficher la vue qui est demandée
     */


    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }

    /**
     * Ajoute des variables globales commune a toutes les vues
     */


    public function addGlobal(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
