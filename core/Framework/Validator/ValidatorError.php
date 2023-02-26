<?php

namespace Core\Framework\Validator;

class ValidatorError
{
    private string $key;

    private string $rule;

    private array $message = [
        'required' => "Le champs %s est requis",
        'email' => "Le champs %s doit etre un email valide",
        'strMin' => "Le champs %s n'atteint pas le minimum de caractères requis",
        'strMax' => "Le champs %s dépsse le nombre de caractères autorisées",
        'confirm' => "Les mots de passe ne sont pas identiques",
        'unique' => "La valeur du champs %s est deja connu du système."
    ];

    public function __construct(string $key, string $rule)
    {
        $this->key = $key;
        $this->rule = $rule;
    }

    public function toString(): string
    {
        if (isset($this->message[$this->rule])) {
            if($this->key === 'mdp'){
                return sprintf($this->message[$this->rule], 'mot de passe'); // Permet de placer rule au niveau de %s 
            }
            else {
                return sprintf($this->message[$this->rule], $this->key); // Permet de placer rule au niveau de %s 
            }
        }
        return $this->rule;
    }
}
