<?php

// // L'espace de noms de cette classe est "Core\Framework\Middleware"
// namespace Core\Framework\Middleware;

// use Core\Framework\Middleware\AbstractMiddleware;
// use Core\Framework\Security\CSRF; // On importe la classe CSRF pour gérer les tokens CSRF
// use Psr\Container\ContainerInterface; // On importe l'interface ContainerInterface pour l'injection de dépendances
// use Psr\Http\Message\ServerRequestInterface; // On importe l'interface ServerRequestInterface pour la gestion des requêtes HTTP

// // On définit la classe CSRFMiddleware qui étend AbstractMiddleware
// class CSRFMiddleware extends AbstractMiddleware
// {

//     private CSRF $csrf; // On définit une propriété CSRF

//     // Le constructeur prend un objet ContainerInterface en argument et initialise la propriété CSRF
//     public function __construct(ContainerInterface $container)
//     {

//         $this->csrf = $container->get(CSRF::class);
//     }

//     // La méthode process gère le middleware CSRF
//     public function process(ServerRequestInterface $request)
//     {
//         // On récupère la méthode HTTP utilisée dans la requête
//         $method = $request->getMethod();

//         // On vérifie que la méthode HTTP nécessite un token CSRF (POST, PUT, PATCH, DELETE)
//         if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) && !in_array($request->getAttribute('_route')->getName(), $this->csrf->getExcludeUrls())) {

//             // On récupère les données envoyées dans la requête
//             $data = $request->getParsedBody();
//             // On récupère le token CSRF à partir des données envoyées
//             $token = $data[$this->csrf->getFormKey()] ?? null;
//             // On peut également récupérer le token directement à partir de la requête
//             //$token = $request->getParsedBody($this->csrf->getFormKey());

//             // On vérifie que le token CSRF est valide
//             if (!$this->csrf->checkToken($token)) {

//                 // Si le token est invalide, on lève une exception
//                 throw new \Exception('Token csrf invalide');
//             }
//         }

//         // On appelle la méthode process de la classe parente (AbstractMiddleware)
//         return parent::process($request);
//     }
// }
