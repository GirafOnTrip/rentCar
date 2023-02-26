<?php

namespace App\User\Action;

use Model\Entity\User;
use Core\Toaster\Toaster;
use GuzzleHttp\Psr7\Response;
use Doctrine\ORM\EntityManager;
use Core\Framework\Auth\UserAuth;
use Core\Framework\Router\Router;
use Doctrine\ORM\EntityRepository;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Container\ContainerInterface;
use Core\Framework\Validator\Validator;
use Core\Framework\Router\RedirectTrait;
use Psr\Http\Message\ServerRequestInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Session\SessionInterface;

class UserAction
{

    use RedirectTrait;

    private ContainerInterface $container;
    private RendererInterface $renderer;
    private Router $router;
    private Toaster $toaster;
    private EntityRepository $repository;
    private SessionInterface $session;


    public function __construct(ContainerInterface $container)
    {

        $this->container = $container;
        $this->renderer = $container->get(RendererInterface::class);
        $this->toaster = $container->get(Toaster::class);
        $this->router = $container->get(Router::class);
        $this->repository = $container->get(EntityManager::class)->getRepository(User::class);
        $this->session = $container->get(SessionInterface::class);
    }

    public function logView(ServerRequestInterface $request)
    {
        return $this->renderer->render('@user/forms');
    }

    public function signIn(ServerRequest $request)
    {

        $auth = $this->container->get(UserAuth::class);
        $data = $request->getParsedBody();
        $validator = new Validator($data);
        $errors = $validator    
            ->required('nom','prenom','mail','mdp','mdp_confirm')
            ->email('mail')
            ->strSize('mdp', 12,50)
            ->confirm('mdp')
            ->isUnique('mail', $this->repository,'mail')
            ->getErrors();

        if ($errors) {
            foreach ($errors as $error) {
                $this->toaster->makeToast($error->toString(), Toaster::ERROR);
            }
            return $this->redirect('user.login');
        }

        $result = $auth->signIn($data);

        if($result !== true) {
            return $result;
        }

        $this->toaster->makeToast("Inscription reussie, vous pouvez vous connecter", Toaster::SUCCESS);
        return $this->redirect('user.login');


    }

    public function login(ServerRequest $request)
    {
        $data = $request->getParsedBody();

        $validator = new Validator($data);
        $errors = $validator
        ->required('mail', 'mdp')
        ->email('mail')
        ->getErrors();

        if($errors) {

            foreach($errors as $error){
                $this->toaster->makeToast($error->toString(),Toaster::ERROR);
            }

            return $this->redirect('user.login');
        }

        $auth = $this->container->get(UserAuth::class);
        $res = $auth->login($data['mail'], $data['mdp']);
        if($res) {
            $this->toaster->makeToast('Connexion reussie', Toaster::SUCCESS);
            return $this->redirect('user.home');
        }

    	$this->toaster->makeToast("Connexion échoué, merci de vérifier email et mot de passe", Toaster::ERROR);
        return $this->redirect('user.login');       
    }

    // On recupere le user en session et on le passe a la vue

    public function home(ServerRequest $request){

        $user = $this->session->get('auth');
        return $this->renderer->render('@user/home', [
            'user'=> $user
        ]);
    }

}



   
