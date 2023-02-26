<?php

namespace Core\Framework\Validator;

use Doctrine\ORM\EntityRepository;

// Permet de valider les données (Sorte de Regex)

class Validator
{
    private array $data;

    private array $errors;

    /** 
     * Enregistre le tableau de données a valider
     * @param array $data tableau de donnêes (habituellement il s'agit du tableau récuperer par $request->getParsedBody())
     */

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Liste les index attendu et obligatoire dans le tableau de données
     * @param string ...$keys liste de chaine de caracteres, "...$key" permet de precisé que l'on s'attend a un nombre indefinie de valeurs
     * @return $this
     */

     // si on utilise ..keys, on ne pourra pas rajouter d'autres attributs derriere car

    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->data) || $this->data[$key] === '' || $this->data[$key] === null) { // Permet de savoir si la clé n'existe pas dans le tableau 
                $this->addError($key, 'required');
            }
        }

        return $this;
    }

    /**
     * S'assure  que le champs est une adresse email valide
     */

    public function email(string $key) : self 
    {

        //filter_var fonction native qui permet de verifier la conformité d'une valeur en fonction d'un filtre cf: php manual
        if(!filter_var($this->data[$key], FILTER_VALIDATE_EMAIL))
        {
            $this->addError($key, 'email');
        }

        return $this;
    }

    /**
     * S'assure que le nombre de caractere d'une chaine soit bien compris entre un minimum et un maximum
     */

    public function strSize(string $key, int $min, int $max): self
    {
        if(!array_key_exists($key, $this->data))
        {
            return $this;
        }
        $length = mb_strlen($this->data[$key]);
        if($length < $min) {
            $this->addError($key, 'strMin');
        }
        if($length > $max) {
            $this->addError($key, 'strMax');
        }
        return $this;
    }

    /**
     * S'assure que le champs saisie possede la meme valeur que son champs de confirmation
     * Si la valeur de $key est "mdp" le champs de confirmation doit absolument se nommer "mdp_confirm"
     */

    public function confirm(string $key): self
    {
        $confirm = $key . '_confirm';
        if(!array_key_exists($key, $this->data))
        {
            return $this;
        }
        if(!array_key_exists($confirm, $this->data))
        {
            return $this;
        }
        if($this->data[$key] !== $this->data[$confirm]) {
            $this->addError($key, 'confirm');
        }
        return $this;
    }

    /**
     * S'assure qu'une valeur soit unique en base de données
     * @param string $key Index du tableau
     * @param EntityRepository $repo repositories doctrine de l element a verifier
     * @param string $field champ a verifier en base de données
     * @return $this
     */

    public function isUnique(string $key, EntityRepository $repo, string $field = 'nom'): self
    {
        // On recupere toutes les entités du repo(De la table)
        $all = $repo->findAll();
        //Créer le nom de la methode utilisable pour recuperer la valeur (exemple: si $field = 'model' alors $method = 'getModel')
        $method = 'get' . ucfirst($field);
        // On boucle sur tous les enregistrements de la base de données
        foreach($all as $item) {
            //On verifie si la valeur saisie par l'utilisateur correspond a une valeur existante en base de données sans tenir compte des accents, si c'est le cas on souleve une erreur
            if(strcasecmp($item->$method(), $this->data[$key]) === 0)
            {
                $this->addError($key, 'unique');
                break;
            }
        }

        return $this;
    }

    /**
     * Renvoie le tableau d'erreur, doit etre appelé seulement après les autres methodes
     * @return $this
     */

    public function getErrors(): ?array
    {
        return $this->errors ?? null;
    }

    private function addError(string $key, string $rule): void
    {
        if (!isset($this->errors[$key])) {
            $this->errors[$key] = new ValidatorError($key, $rule);
        }
    }
}
