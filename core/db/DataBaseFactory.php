<?php

namespace Core\Db;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;

class DataBaseFactory
{
  public function __invoke(ContainerInterface $container): ?EntityManager // Permet d'utiliser un objet comme une fonction
  {
    $paths = [dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'model/entity'];

    $isDevMode = $container->get("doctrine.devmode");
    $dbparams = [
      "host" => "127.0.0.1:8889", // Rajout pour config mac // A mettre en commentaire sur windows
      "driver" => $container->get("doctrine.driver"),
      "user" => $container->get("doctrine.user"),
      "password" => $container->get("doctrine.mdp"),
      "dbname" => $container->get("doctrine.dbname"),
      "driverOptions" => [
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
      ]
    ];

    $config = ORMSetup::createAnnotationMetadataConfiguration($paths, $isDevMode);

    try {
      $conn = DriverManager::getConnection($dbparams);
      return EntityManager::create($conn, $config);
    } catch (\Exception $e) {
      echo "[ERREUR] : " . $e->getMessage();
      die;
    }
  }
}

// Ceci est un script PHP qui établit une connexion à une base de données en utilisant la bibliothèque ORM (Object Relational Mapping) de Doctrine.
// La classe DataBaseFactory est invoquée en tant que conteneur PSR-11 et prend une instance de ContainerInterface en tant que paramètre.
// Il récupère divers paramètres de connexion du conteneur, notamment le pilote de base de données, l'utilisateur, le mot de passe et le nom,
// et utilise ces paramètres pour créer une connexion Doctrine DBAL.
// La connexion est ensuite transmise à la classe EntityManager pour créer une instance d'un objet EntityManager,
// qui sert de point d'entrée principal à la fonctionnalité ORM de Doctrine. Si des exceptions sont levées pendant ce processus,
// le script attrape l'exception et affiche un message d'erreur avant d'arrêter l'exécution du script.
