<?php

namespace Core\Framework\Router;

class Route
{
    private string $name;

    private $callable;

    private array $params;

    /** 
     * On enregistre les informations liée a la route
     * @param string $name // Nom de la route (Exemple : user.login)
     * @param $callable // Fonction de controller a appeler lors du match de la route
     * @param array $params // Tableau de parametre de la route
     */

    public function __construct(string $name, $callable, array $params)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->params = $params;
    }


    public function getName()
    {
        return $this->name;
    }

    /** 
     * Retourne la fonction de controller lié a la route
     */

    public function getCallback()
    {
        return $this->callable;
    }

    public function getParams()
    {
        return $this->params;
    }
}

// Ceci est une classe en langage PHP appelée Route.
// Elle fait partie du namespace Core\Framework\Router.
// Cette classe est utilisée pour stocker des informations sur une route, y compris son nom, sa fonction de rappel et tous les paramètres associés.

// La classe possède trois propriétés privées:

// $name: une chaîne qui stocke le nom de la route.
// $callable: qui stocke une fonction de rappel qui sera exécutée lorsque la route est correspondante.
// $params: un tableau qui stocke tous les paramètres associés à la route.
// La classe possède trois méthodes publiques:

// getName(): renvoie le nom de la route.
// getCallback(): renvoie la fonction de rappel associée à la route.
// getParams(): renvoie les paramètres associés à la route.
// La classe prend en entrée trois paramètres dans son constructeur et les affecte aux propriétés de la classe.

// Cette classe est utile pour stocker et organiser les informations sur les routes dans une application, ce qui facilite la gestion et la modification de ces dernières.
