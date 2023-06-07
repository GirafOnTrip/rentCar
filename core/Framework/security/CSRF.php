<?php

// L'espace de noms de cette classe est "Core\Framework\Security"
namespace Core\Framework\Security;

use Core\Session\SessionInterface; // On importe l'interface SessionInterface pour la gestion des sessions
use Psr\Container\ContainerInterface; // On importe l'interface ContainerInterface pour la gestion des conteneurs

// On définit la classe CSRF
class CSRF
{

    // On définit les propriétés privées de la classe
    private string $sessionKey = '_csrf_token'; // Clé pour stocker le token dans la session
    private string $formKey = '_crsf'; // Clé pour stocker le token dans le formulaire HTML
    private SessionInterface $session; // Interface de gestion des sessions
    private array $excludeUrls;

    // Le constructeur de la classe prend en paramètre un conteneur d'injection de dépendances (ContainerInterface)
    public function __construct(ContainerInterface $container, array $excludeUrls = [])
    {
        $this->session = $container->get(SessionInterface::class); // On récupère l'interface de gestion des sessions depuis le conteneur
        $this->excludeUrls = $excludeUrls;
    }

    // La méthode generateToken permet de générer un nouveau token CSRF et le stocke dans la session et le retourne dans un champ de formulaire HTML
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(32)); // Génère un nouveau token CSRF
        $this->session->setArray($this->sessionKey, $token); // Stocke le token dans la session
        $this->limitToken(); // Limite le nombre de token dans la session
        return "<input type='hidden' name='{$this->formKey}' value='{$token}'>"; // Retourne le token dans un champ de formulaire HTML
    }

    // La méthode checkToken permet de vérifier si le token CSRF est valide ou non
    public function checkToken(string $token = null): bool
    {
        if (!is_null($token)) { // Si un token est fourni en argument
            $tokens = $this->session->get($this->sessionKey, []); // On récupère tous les tokens stockés dans la session
            $key = array_search($token, $tokens, true); // On cherche le token fourni dans le tableau de tokens stockés dans la session
            if ($key !== false) { // Si le token est trouvé
                $this->consumeToken($token); // On consomme le token (c'est-à-dire qu'on le supprime de la session)
                return true; // Le token est valide
            }

            return false; // Le token n'est pas valide
        }

        return false; // Le token n'est pas valide
    }

    // La méthode limitToken permet de limiter le nombre de token stockés dans la session
    private function limitToken(): void
    {
        $tokens = $this->session->get($this->sessionKey, []); // On récupère tous les tokens stockés dans la session
        if (count($tokens) > 10) { // Si le nombre de tokens stockés dans la session est supérieur à 10
            array_shift($tokens); // On supprime le premier token stocké dans la session (le plus ancien)
            $this->session->set($this->sessionKey, $tokens); // On met à jour la session avec la nouvelle liste de tokens
        }
    }

    // La méthode consumeToken permet de consommer un token (c'est-à-dire de le supprimer de la session)
    private function consumeToken(string $token): void
    {

        // La fonction array_reduce s'attend à un tableau et à une fonction qui sera appliquée sur chaque élément du tableau. Elle prend chaque élément et applique la fonction à chaque fois en retournant une valeur accumulée à chaque itération. Ici, la fonction utilise la variable $token définie dans la fonction parente pour supprimer le token correspondant de la liste des tokens stockés en session.
        $tokens = array_reduce($this->session->get($this->sessionKey, []), function ($tok) use ($token) {

            if ($tok !== $token) {
                return $tok;
            }
        }, []);

        // Enfin, la méthode set de l'objet SessionInterface est appelée pour mettre à jour la session avec la nouvelle liste de tokens.
        $this->session->set($this->sessionKey, $tokens);
    }

    public function getFormKey(): string
    {
        return $this->formKey;
    }

    public function getExcludeUrls(): array
    {
        return $this->excludeUrls;
    }
}
