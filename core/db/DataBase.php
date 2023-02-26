<?php

namespace Core\Db;

use PDO;

use PDOException;

class Database
{

    private string $host;
    private string $user;
    private string $dbname;
    private string $mdp;
    private string $char;
    
    public PDO $connection;
    public static Database $instance;

    // D'abord vérifier si la connexion existe sinon la créer //
    // Ici on crée un objet pour que la connexion a la BDD ne se fasse qu'une seul fois 

    private function __construct(string $host, string $dbname, string $user, string $mdp, string $char = 'utf8')
    {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->mdp = $mdp;
        $this->char = $char;

        // "self" remplace l'objet "Database"

        try {
            $this->connection = new PDO("mysql:host={$this->host}; dbname = {$this->dbname};charset={$this->char}", $this->user, $this->mdp);
        } catch (PDOException $e) {
            echo "[ERREUR] => {$e->getMessage()}";
            die;
        }
    }

    // Ici on crée la fonction qui permet d'instancier la DB 
    public static function getInstance(string $host, string $dbname, string $user, string $mdp, string $char = 'utf8')
    {
        if (!isset(self::$instance) or empty(self::$instance)) {
            self::$instance = new Database($host, $dbname, $user, $mdp, $char);
        }
        return self::$instance;
    }
}

// Ceci est une classe appelée Database qui est dans un namespace appelé Core\db. Il utilise les classes PDO et PDOException de l'extension PHP Data Objects (PDO).
// La classe possède plusieurs propriétés privées : $host, $user, $dbname, $mdp et $char.
// Elle possède également une propriété publique appelée $connection et une propriété statique publique appelée $instance.
// La classe possède un constructeur privé qui prend en compte les mêmes cinq propriétés que les propriétés de la classe et les affecte aux propriétés de la classe.
// Il contient également un bloc try-catch qui tente de créer un nouvel objet PDO avec les informations de connexion fournies et l'affecte à la propriété $connection.
// Si cela échoue, il affiche un message d'erreur et arrête le script.

// La classe possède également une méthode statique publique appelée "getInstance" qui prend en compte les mêmes cinq propriétés que les propriétés de la classe
// et les affecte aux propriétés de la classe.
// Il vérifie tout d'abord si la propriété $instance n'est pas définie ou vide, et si c'est le cas,
// il crée une nouvelle instance de la classe Database et l'affecte à la propriété $instance.
// Il renvoie ensuite la propriété $instance. Cette méthode garantit qu'il n'y a qu'une seule instance de la classe Database et que la même connexion est utilisée dans tout le script.
