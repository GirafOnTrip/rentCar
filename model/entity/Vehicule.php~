<?php

namespace Model\Entity;

use Doctrine\ORM\Mapping as ORM; // On renomme l'objet Mapping  en ORM

/**
 * @ORM\Table(name = "vehicule")
 * @ORM\Entity
 */

class Vehicule
{
    /**
     * @ORM\ID
     * @ORM\Column(type="integer", name="id") 
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */

    private int $id;

    /**
     * @ORM\Column(type="string" , name="modele" , length="55")
     * @var string
     */

    private string $model;

    /**
     * @ORM\JoinColumn(referencedColumnName="id", name="id_marque", onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity="Marque", inversedBy="vehicules")
     * @var Marque
     */

    private Marque $marque;

    /**
     * @ORM\Column(type="string", length="10")
     * @var string
     */

    private string $couleur;

    /**
     * @ORM\Column(type="string", name="img_path")
     * @var string
     */

     private string $imgPath;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set model.
     *
     * @param string $model
     *
     * @return Vehicule
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set couleur.
     *
     * @param string $couleur
     *
     * @return Vehicule
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur.
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set marque.
     *
     * @param \Model\Entity\Marque|null $marque
     *
     * @return Vehicule
     */
    public function setMarque(\Model\Entity\Marque $marque = null)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque.
     *
     * @return \Model\Entity\Marque|null
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set imgPath.
     *
     * @param string $imgPath
     *
     * @return Vehicule
     */
    public function setImgPath($imgPath)
    {
        $this->imgPath = $imgPath;
    
        return $this;
    }

    /**
     * Get imgPath.
     *
     * @return string
     */
    public function getImgPath()
    {
        return $this->imgPath;
    }
}
