<?php

namespace Core\Framework\Middleware;

use Core\Framework\Router\RedirectTrait;
use Core\Framework\Router\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Cache\Traits\RedisTrait;

class UserAuthMiddleware extends AbstractMiddleware // Permet de retirer le "/" a la fin d'une URL
{

    use RedirectTrait;

    private ContainerInterface $container;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
       $this->container = $container;
       $this->router = $container->get(Router::class);
    }

    public function process(ServerRequestInterface $request)
    {
        
        $uri = $request->getUri()->getPath();

        //On vérifie si l'url commence par '/user' et n'est pas egale a '/user/login'
        if(str_starts_with($uri,'/user')) {
            $auth = $this->container->get(UserAuth::class);
            if (!$auth->isLogged() || !$auth->isUser()) {

                $toaster = $this->container->get(Toaster::class);
                $toaster->makeToast("Veuillez vous connecter pour continuer");
                return $this->redirect('user.login');
            }
        }

        return parent::process($request); // Si oublie, elle ne va pas jusqu'au controller, lie le parent precedent
    }
}
