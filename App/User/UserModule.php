<?php

namespace App\User;

use App\User\Action\UserAction;
use Core\Framework\Router\Router;
// use Core\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\AbstractClass\AbstractModule;

 
class UserModule extends AbstractModule
{

 
    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;
    // private SessionInterface $session;

 
    public const DEFINITIONS = __DIR__ . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php";

 
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->router = $container->get(Router::class);
        $userAction = $container->get(UserAction::class);

 
        $this->renderer->addPath("user", __DIR__ . DIRECTORY_SEPARATOR . "view");

        $this->router->post("/newUser", [$userAction, "signin"], "user.new");
        $this->router->get("/login", [$userAction, "logView"], "user.login");

        $this->router->post("/connexion", [$userAction, "login"], "user.connexion");
        $this->router->get("/user/home", [$userAction, "home"], "user.home");

        // $user = $this->session->get('auth');

        // if($user){
        //     $this->renderer->addGlobal('user', $user);
        // }
    }


 
}

