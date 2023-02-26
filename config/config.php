<?php

use Core\Toaster\Toaster;
use Core\Toaster\ToasterTwigExtension;
use Core\Db\DatabaseFactory;
use Core\Session\PHPSession;
use Doctrine\ORM\EntityManager;
use Core\Framework\Router\Router;
use Core\Session\SessionInterface;
use Core\Framework\Renderer\RendererInterface;
use Core\Framework\Router\RouterTwigExtension;
use Core\Framework\Renderer\TwigRendererFactory;
use Core\Framework\TwigExtensions\AssetsTwigExtension;

// Information de la base de données 

return [
    "doctrine.user" => "root",
    "doctrine.dbname" => "rentcar",
    "doctrine.mdp" => "",
    "doctrine.driver" => "pdo_mysql",
    "doctrine.devmode" => "true",

    //Chemin par defaut des vues 
    "config.viewPath" => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'view',

    "twig.extensions" => [
        RouterTwigExtension::class,
        ToasterTwigExtension::class,
        AssetsTwigExtension::class
    ],

    // On explique a PHP DI comment faire 
    Router::class => \DI\create(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    EntityManager::class => \DI\factory(DatabaseFactory::class),
    SessionInterface::class => \DI\get(PHPSession::class), // si la class SessionInterface est appelé, on crée un nouvel objet PHPSession
    Toaster::class => \DI\autowire()
];
