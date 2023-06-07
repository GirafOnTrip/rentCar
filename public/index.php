<?php

use Core\App;
use App\Car\CarModule;
use App\Home\HomeModule;
use App\User\UserModule;
use DI\ContainerBuilder;
use App\Admin\AdminModule;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Core\Framework\Middleware\RouterMiddleware;
use Core\Framework\Middleware\NotFoundMiddleware;
use Core\Framework\Middleware\AdminAuthMiddleware;
use Core\Framework\Middleware\CSRFMiddleware;
use Core\Framework\Middleware\TrailingSlashMiddleware;
use Core\Framework\Middleware\RouterDispatcherMiddleware;
use Core\Framework\Middleware\UserAuthMiddleware;

//Inclusion de lautoloader de composer

require dirname(__DIR__) . "/vendor/autoload.php";

// Bien ajouter chaque module dans ce tableau
// Declaration du tableau de modules a charger
$modules = [
    HomeModule::class,
    CarModule::class,
    AdminModule::class,
    UserModule::class
];

//Instanciation du builder du container de dependance, le builder permet de construire l'objet container de de2pendance
// Mais ce n'est pas le container de dependances
$builder = new ContainerBuilder();
// AJout de la feuille de configuration principale
$builder->addDefinitions(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php');

foreach ($modules as $module) {
    if (!is_null($module::DEFINITIONS)) {
        //Si les moduls possédent une feuille de configuration personnalise2 on l'ajoute aussi
        $builder->addDefinitions($module::DEFINITIONS);
    }
}

//On recupere l'instance du container de dependance
$container = $builder->build();

//On instancie notre application en lui donnant la liste des modules et le container de dependances
$app = new App($container, $modules);

// On link le premier middleware de la chaine de responsabilité a l'application
// Puis on ajoute les middlewares suivants en leur passant le container de dependances si besoin 
$app->linkFirst(new TrailingSlashMiddleware())
    ->linkWith(new RouterMiddleware($container))
    // ->linkWith(new CSRFMiddleware($container))
    ->linkWith(new AdminAuthMiddleware($container))
    ->linkWith(new RouterDispatcherMiddleware())
    ->linkWith(new NotFoundMiddleware)
    ->linkWith(new UserAuthMiddleware($container));

//Si l'index n'est pas executé a partir de la CLI (Command Line Interface)
if (php_sapi_name() !== 'cli') {

    //On recupere la reponse de notre application en lancant la methode 'run' et en lui passant un objet ServerRequest 
    // rempli avec toutes les informations de la requete envoyé par la machine client (le navigateur)
    $reponse = $app->run(ServerRequest::fromGlobals());

    // On renvoi le reponse au serveur apres avoir transformer le retour de l 'application en une reponse comprehensible par la machine client ( navigateur)
    send($reponse);
}

// Ceci est un script PHP simple qui crée une nouvelle instance de la classe App et l'exécute avec la méthode ServerRequest::fromGlobals().
// Le script commence par importer les classes nécessaires à partir des espaces de noms GuzzleHttp et Core.
// Il importe également la fonction send à partir de l'espace de nom Http\Response.
// Ensuite, il inclut le fichier autoload à partir du répertoire vendor.

// Le script crée ensuite une nouvelle instance de la classe App et l'affecte à la variable $app.
// Il exécute ensuite la méthode run() de la classe App et lui passe la méthode ServerRequest::fromGlobals(),
// qui crée un nouvel objet ServerRequest en utilisant les valeurs des variables globales PHP ($_SERVER, $_GET, $_POST, etc.).
// La réponse de la méthode run() est affectée à la variable $response.

// Enfin, le script appelle la fonction send() et lui passe la variable $response.
// La fonction send() envoie la réponse au client (navigateur).
// Le script ci-dessus est un exemple de la façon dont vous pouvez utiliser la classe App pour gérer une demande HTTP et envoyer une réponse HTTP.
