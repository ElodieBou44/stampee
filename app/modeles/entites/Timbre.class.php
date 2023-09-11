<?php

/**
 * Classe de l'entité Timbre
 *
 */
class Timbre extends Entite
{
  protected $timbre_id;
  protected $timbre_nom;
  protected $timbre_date_creation;
  protected $timbre_pays;
  protected $timbre_condition;
  protected $timbre_dimensions; 
  protected $timbre_certification;
  protected $timbre_tirage;
  protected $timbre_couleurs;
  protected $timbre_enchere_id;
  protected $timbre_utilisateur_id;
  protected $messageErreur; 

  protected $erreurs = array();

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getTimbre_id()                 { return $this->timbre_id; }
  public function getTimbre_nom()                { return $this->timbre_nom; }
  public function getTimbre_pays()               { return $this->timbre_pays; }
  public function getTimbre_condition()          { return $this->timbre_condition; }
  public function getTimbre_dimensions()         { return $this->timbre_dimensions; }
  public function getTimbre_certification()      { return $this->timbre_certification; }
  public function getTimbre_tirage()             { return $this->timbre_tirage; }
  public function getTimbre_couleurs()           { return $this->timbre_couleurs; }
  public function getTimbre_enchere_id()         { return $this->timbre_enchere_id; }
  public function getErreurs()                   { return $this->erreurs; }

    /**
     * Mutateur de la propriété timbre_id 
     * @param int $timbre_id
     * @return $this
     */    
    public function setTimbre_id($timbre_id) {
        if (preg_match('/^[1-9]\d*$/', $timbre_id)) {
            $this->timbre_id = $timbre_id;
        } else {
            $this->erreurs['timbre_id'] = "L'id du timbre doit être une valeur entière strictement positive.";
        }
    }

    /**
     * Mutateur de la propriété timbre_nom 
     * @param string $timbre_nom
     * @return $this
     */    
    public function setTimbre_nom($timbre_nom) {
        unset($this->erreurs['timbre_nom']); //Initialisation de la variable erreurs 
        $pattern = "/^.{2,250}$/"; // Vérification du format du nom
        if (preg_match($pattern, $timbre_nom)) {
            $this->timbre_nom = $timbre_nom;
        } else {
        $this->erreurs['timbre_nom'] = "Le nom du timbre doit avoir entre 2 et 250 caractères.";
        }
    }

    /**
     * Mutateur de la propriété timbre_date_creation
     * @param string $timbre_date_creation 
     * @return $this
     */    
    public function setTimbre_date_creation($timbre_date_creation) {
        unset($this->erreurs['timbre_date_creation']); //Initialisation de la variable erreurs 
        $pattern = "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/"; // Vérification du format de la date
        if (preg_match($pattern, $timbre_date_creation)) {
            $this->timbre_date_creation= $timbre_date_creation;
        } else {
        $this->erreurs['timbre_date_creation'] = "Le date de création du timbre doit être au format AAAA-MM-JJ.";
        }
    }

    /**
     * Mutateur de la propriété timbre_pays 
     * @param string $timbre_pays
     * @return $this
     */    
    public function setTimbre_pays($timbre_pays) {
        unset($this->erreurs['timbre_pays']); //Initialisation de la variable erreurs 
        $pattern = "/^[a-zA-ZàâäçéèêëîïôöùûüÿñæœÀÂÄÇÉÈÊËÎÏÔÖÙÛÜŸÑÆŒ\-]+$/"; 
        // Vérification du format du nom du pays 
        if (preg_match($pattern, $timbre_pays)) {
            $this->timbre_pays = $timbre_pays;
        } else {
        $this->erreurs['timbre_pays'] = "Format incorrect.";
        }
    }

    /**
     * Mutateur de la propriété timbre_condition 
     * @param string $timbre_condition
     * @return $this
     */    
    public function setTimbre_condition($timbre_condition) {
        $condition_acceptees = [        
            "Parfait",
            "Excellente", 
            "Bonne",
            "Moyenne", 
            "Endommagé"
        ];
        // Vérification d'une condition valide
        if (in_array($timbre_condition, $condition_acceptees)) {
            $this->timbre_condition = $timbre_condition;
        } else {
            $this->erreurs['timbre_condition'] = "La condition sélectionnée n'est pas valide.";
        }
    }

    /**
     * Mutateur de la propriété timbre_dimensions
     * @param string $timbre_dimensions
     * @return $this
     */    
    public function setTimbre_dimensions($timbre_dimensions) {
        unset($this->erreurs['timbre_dimensions']); //Initialisation de la variable erreurs 
        $timbre_dimensions = trim($timbre_dimensions); 
        $pattern = "/^\d+(\.\d+)?cm x \d+(\.\d+)?cm$/"; // Vérification du format des dimensions du timbre
        if (preg_match($pattern, $timbre_dimensions)) {
            $this->timbre_dimensions = $timbre_dimensions;
        } else {
        $this->erreurs['timbre_dimensions'] = "Seulement le format '00cm x 00cm' est accepté.";
        }
    }

    /**
     * Mutateur de la propriété timbre_certification
     * @param string $timbre_certification
     * @return $this
     */    
    public function setTimbre_certification($timbre_certification) {
        unset($this->erreurs['timbre_certification']); //Initialisation de la variable erreurs 
        if($timbre_certification == "oui" || $timbre_certification == "non"){

            $this->timbre_certification = $timbre_certification;
        } else {
        $this->erreurs['timbre_certification'] = "Seulement les valeurs 'Oui' ou 'Non' sont acceptées.";
        }
    }

    /**
     * Mutateur de la propriété timbre_tirage
     * @param string $timbre_tirage
     * @return $this
     */    
    public function setTimbre_tirage($timbre_tirage) {
        unset($this->erreurs['timbre_tirage']); //Initialisation de la variable erreurs 
        $pattern = "/^\d+$/"; // Vérification du format du nombre de tirages
        if (preg_match($pattern, $timbre_tirage)) {
            $this->timbre_tirage= $timbre_tirage;
        } else {
        $this->erreurs['timbre_tirage'] = "Seulement un format numérique est accepté.";
        }
    }

    /**
     * Mutateur de la propriété timbre_couleurs 
     * @param string $timbre_couleurs
     * @return $this
     */    
    public function setTimbre_couleurs($timbre_couleurs) {
        unset($this->erreurs['timbre_couleurs']); //Initialisation de la variable erreurs 
        $timbre_couleurs = strtolower(trim($timbre_couleurs)); 
        $pattern = "/^[a-zA-Z]{2,}(-[a-zA-Z]{2,})*$/"; // Vérification du format du prix
        if (preg_match($pattern, $timbre_couleurs)) {
            $this->timbre_couleurs = $timbre_couleurs;
        } else {
        $this->erreurs['timbre_couleurs'] = "La couleur doit contenir au moins 2 caractères.";
        }
    }

    /**
     * Mutateur de la propriété timbre_enchere_id 
     * @param int $timbre_enchere_id 
     * @return $this
     */    
    public function setTimbre_enchere_id ($timbre_enchere_id ) {
        if (preg_match('/^[1-9]\d*$/', $timbre_enchere_id )) {
            $this->timbre_enchere_id  = $timbre_enchere_id ;
        } else {
            $this->erreurs['timbre_enchere_id '] = "L'id de l'enchère du timbre doit être une valeur entière strictement positive.";
        }
    }

    /**
   * Mutateur de la propriété timbre_utilisateur_id
   * @param string $timbre_utilisateur_id
   * @return $this
   */    
  public function setTimbre_utilisateur_id($timbre_utilisateur_id) {
    echo($timbre_utilisateur_id); 
    $timbre_utilisateur_id = $_POST['timbre_utilisateur_id'];
    if (preg_match('/^[1-9]\d*$/', $timbre_utilisateur_id)) {
        $this->timbre_utilisateur_id = $timbre_utilisateur_id;
    } else {
        $this->erreurs['timbre_utilisateur_id'] = "L'id de l'utilisateur doit être une valeur entière strictement positive.";
    }
  }
}

