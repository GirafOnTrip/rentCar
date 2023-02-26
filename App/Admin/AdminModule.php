<?php

namespace App\Admin;

use App\Admin\Action\AuthAction;
use App\Admin\Action\AdminAction;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

class AdminModule extends AbstractModule  
{

    private ContainerInterface $container;

    private Router $router;

    private RendererInterface $renderer;


    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->router = $container->get(Router::class); // Router permet de définir les routes des URL
        $this->renderer = $container->get(RendererInterface::class); // Renderer permet d'afficher le rendu de la page 
        $authAction = $container->get(AuthAction::class);
        $adminAction = $container->get(AdminAction::class);
        
        // Le noms de la route est toujours le dernier parametre renseigner

        // Les routes pour notre admin se trouve ici 
        $this->renderer->addPath('admin', __DIR__ . DIRECTORY_SEPARATOR .'view');  
        $this->router->get('/admin/login', [$authAction, 'login'], 'admin.login'); // Attribution du nom de la route 
        $this->router->post('/admin/login', [$authAction, 'login']);
        $this->router->get('/admin/home', [$adminAction, 'home'], 'admin.home');
        $this->router->get('/admin/logout', [$authAction, 'logout'], 'admin.logout');
    }

    // public function indexAdmin()
    // {
    //     return $this->renderer->render('@admin/indexAdmin');
    // }
}