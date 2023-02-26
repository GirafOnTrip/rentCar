<?php

// Objet utilitaire = Il sera appeler par le controller

namespace Core\Framework\Auth;

use Model\Entity\Admin;
use Core\Session\PHPSession;
use Doctrine\ORM\EntityManager;
use Core\Session\SessionInterface;

class AdminAuth
{

    private EntityManager $manager;

    private SessionInterface $session;

    public function __construct(EntityManager $manager, SessionInterface $session)
    {
        $this->manager = $manager;
        $this->session = $session;
    }

    public function login(string $email, string $password): bool
    {
        $admin = $this->manager->getRepository(Admin::class)
                ->findOneBy(["mail" => $email]);
        if ($admin && password_verify($password, $admin->getPass())){

            $this->session->set('auth', $admin);
            return true;
        }

        return false;
    }

    public function logout():void
    {
        $this->session->delete('auth');
    }

    public function isLogged():bool
    {
        return $this->session->has('auth');
    }

    public function isAdmin() : bool
    {
        if($this->isLogged()){
            return $this->session->get('auth') instanceof Admin;
        }

        return false;

    }
}