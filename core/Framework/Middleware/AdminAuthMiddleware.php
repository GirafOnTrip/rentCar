<?php
namespace Core\Framework\Middleware;

use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Core\Framework\Auth\AdminAuth;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Middleware\AbstractMiddleware;

// Le middleware a une propriété et deux methodes

/**
 * Verifie si la route est protégé grace au debut de l'url,
 * si oui s'assure que l'utilisateur a le droit d'y acceder
 */

class AdminAuthMiddleware extends AbstractMiddleware
{
    private ContainerInterface $container;
    private Toaster $toaster;

    public function __construct(ContainerInterface $container){

        $this->container = $container;
        $this->toaster = $container->get(Toaster::class);
        
    }

    public function process(ServerRequestInterface $request)
    {
        $uri = $request->getUri()->getPath();
        //On vérifie si l'url commence par '/admin' et n'est pas egale a '/admin/login'
        if (str_starts_with($uri, '/admin') && $uri !== '/admin/login')
        {
            //On recupere l'objet qui gere l'administrateur et connecté et qu'il s'agit bien d'un administrateur
            $auth = $this->container->get(AdminAuth::class);

            if(!$auth->isLogged()) {

                // SI personne n'est connecté on renvoi un message en consequence

                if(!$auth->isAdmin()){
                    $this->toaster->makeToast("Vous ne possédez pas les droits d'accès", Toaster::ERROR);

                } elseif(!$auth->isLogged()){
                    //Si quelqu'un est connecté mais n'est pas un administrateur on lui refuse l'accès
                    $this->toaster->makeToast("Vous devez etre connecté pour accéder à cette page", Toaster::ERROR);
                }
                return (new Response())
                ->withHeader('Location', '/');
            }
        }

        return parent::process($request);
    }


}