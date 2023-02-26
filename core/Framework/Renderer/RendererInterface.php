<?php

namespace Core\Framework\Renderer;

interface RendererInterface
{
    public function addPath(string $namespace, ?string $path = null): void;

    public function render(string $view, array $params = []): string;
}

// Cette interface en langage PHP, intitulée RendererInterface, fait partie du namespace Core\Framework\Renderer.
// Elle définit un contrat pour une classe, en spécifiant un ensemble de méthodes que la classe doit implémenter.

// Elle définit deux méthodes publiques :

// addPath : Cette méthode prend en paramètre une chaîne de caractères $namespace et une chaîne de caractères optionnelle $path.
// Elle permet d'ajouter un chemin à un espace de nom. Si le chemin n'est pas fourni, l'espace de nom sera affecté à l'espace de nom par défaut.

// render : Cette méthode prend en paramètre une chaîne de caractères $view et un tableau $params.
// Elle permet de rendre une vue et de retourner la sortie sous forme de chaîne de caractères.

// Les classes qui implémentent cette interface doivent fournir une implémentation pour ces deux méthodes.

// Cette interface est utile pour s'assurer que toutes les classes qui la implémentent ont les mêmes méthodes et peuvent être utilisées de manière interchangeable.
