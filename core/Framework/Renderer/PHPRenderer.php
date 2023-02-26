<?php

namespace Core\Framework\Renderer; // Dossier imaginaire qui relie tous les éléments aux mêmes noms

class PHPRenderer implements RendererInterface

{

    const DEFAULT_NAMESPACE = "__MAIN";

    private array $paths = [];

    private array $globals = [];

    public function __construct(string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    public function addPath(string $namespace, ?string $path = null): void
    {
        if (is_null($path)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    // $renderer->render('@blog/addVehicule')

    //$renderer->render('header')

    public function render(string $view, array $params = []): string
    {
        if ($this->hasNameSpace($view)) {
            $path = $this->replaceNameSpace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    private function hasNameSpace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function replaceNameSpace(string $view): string
    {
        $namespace = substr($view, 1, strpos($view, '/') - 1);
        $str = str_replace('@' . $namespace, $this->paths[$namespace], $view); // Ce qu'on recherche , Ce qu'on remplace , et où tu le fais 
        return str_replace('/', '\\', $str);
    }
}

// Ceci est une classe en langage PHP qui implémente l'interface RendererInterface.
// La classe a une constante, DEFAULT_NAMESPACE, avec une valeur de "__MAIN" et une propriété privée, $paths, qui est un tableau.
// La classe a trois méthodes : addPath, render, hasNameSpace et replaceNameSpace.

// La méthode addPath permet au développeur d'ajouter un chemin vers le tableau $paths, avec la clé étant un espace de nom et la valeur étant le chemin.
// Si un chemin n'est pas fourni, il affecte l'espace de nom à la clé DEFAULT_NAMESPACE dans le tableau $paths.

// La méthode render prend en entrée deux paramètres, une chaîne de caractères $view et un tableau $params.
// La méthode vérifie si le paramètre $view a un espace de nom en appelant la méthode hasNameSpace, qui vérifie si le premier caractère de la chaîne $view est "@".
// S'il a un espace de nom, il utilise la méthode replaceNameSpace pour remplacer l'espace de nom par le chemin associé dans le tableau $paths, et ajoute l'extension '.php' au chemin.
// S'il n'a pas d'espace de nom, il affecte la valeur de $path à l'espace de nom par défaut, la vue et l'extension de fichier ".php".
// Ensuite, il utilise les fonctions ob_start et ob_get_clean pour stocker la sortie du fichier requis dans une variable et la renvoyer.

// La méthode hasNameSpace est une méthode privée qui renvoie un booléen en fonction de si le premier caractère de la chaîne d'entrée est "@" ou non.

// La méthode replaceNameSpace est une méthode privée qui prend en entrée une chaîne de caractères $view et renvoie la chaîne modifiée.
// La méthode obtient tout d'abord l'espace de nom à partir de $view en utilisant la fonction substr pour obtenir les caractères après le symbole "@" et avant le symbole "/".
// Ensuite, elle utilise la fonction str_replace pour remplacer l'espace de nom @ par le chemin associé à cet espace de nom dans le tableau $paths.
// Enfin, elle remplace tous les caractères "/" par des caractères "" et renvoie la chaîne modifiée.
