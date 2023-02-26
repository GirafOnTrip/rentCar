<?php

namespace Core\Framework\AbstractClass;

/**
 * Un module represente un ensemble de page qui sont chargé d'une responsabilité particuliere
 * (Exemple : CarModule est chargé a tout ce qui touche au vehicule ajout, modification, suppression, acces etc)
 * Chaque module que l'on souhaite charger dans l'application doit etre declarer dans $modules dans /public/index.php
 */

abstract class AbstractModule
{

    /**
     * Chemin du fichier 
     */
    
    public const DEFINITIONS = null;
}
