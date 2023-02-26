<?php
namespace Core\Framework\Renderer;


use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\TwigRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// On instancie notre TwigRenderer

class TwigRendererFactory {

    // __invoke :  Methode magique qui est appele au moment ou l'on essaie d'appeler un objet comme une fonction
    /**
     * EXEMPLE: $twig = TwigRendererFactory()
     */

    
    public function __invoke(ContainerInterface $container): ?TwigRenderer 
    {
        $loader = new FilesystemLoader($container->get('config.viewPath'));
        $twig = new Environment($loader, []);

        // Recupere la liste d'extensions Twig a charger

        $extensions = $container->get("twig.extensions");

        // Boucle sur la liste d'extension et ajout a Twig
        foreach ($extensions as $extension) {
            $twig->addExtension($container->get($extension));
        }

        return new TwigRenderer($loader, $twig);
    }
}