<?php

/**
 * Classe de l'entité Enchere
 *
 */
class Enchere extends Entite
{
  protected $enchere_id = 0;
  protected $enchere_periode_activite_debut;
  protected $enchere_prix_plancher;
  protected $enchere_quantite_mises = 0;
  protected $enchere_cdc_lord = 0;
  protected $enchere_periode_activite_fin; 
  protected $enchere_offre_actuelle_prix = null;
  protected $enchere_offre_actuelle_nom_membre = null;
  protected $enchere_utilisateur_id = 0;
  protected $messageErreur; 

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getEnchere_id()                              { return $this->enchere_id; }
  public function getEnchere_periode_activite_debut()          { return $this->enchere_periode_activite_debut; }
  public function getEnchere_prix_plancher()                   { return $this->enchere_prix_plancher; }
  public function getEnchere_quantite_mises()                  { return $this->enchere_quantite_mises; }
  public function getEnchere_cdc_lord()                        { return $this->enchere_cdc_lord; }
  public function getEnchere_periode_activite_fin()            { return $this->enchere_periode_activite_fin; }
  public function getEnchere_offre_actuelle_prix()             { return $this->enchere_offre_actuelle_prix; }
  public function getEnchere_offre_actuelle_nom_membre()       { return $this->enchere_offre_actuelle_nom_membre; }
  public function getEnchere_utilisateur_id()                  { return $this->enchere_utilisateur_id; }
  public function getErreurs()                                 { return $this->erreurs; }

  /**
   * Mutateur de la propriété enchere_id 
   * @param int $enchere_id
   * @return $this
   */    
  public function setEnchere_id($enchere_id) {
    if (preg_match('/^[1-9]\d*$/', $enchere_id)) {
        $this->enchere_id = $enchere_id;
    } else {
        $this->erreurs['enchere_id'] = "L'id de l'enchère doit être une valeur entière strictement positive.";
    }
  }

  /**
   * Mutateur de la propriété enchere_periode_activite_debut
   * @param string $enchere_periode_activite_debut
   * @return $this
   */    
  public function setEnchere_periode_activite_debut($enchere_periode_activite_debut) {
    unset($this->erreurs['enchere_periode_activite_debut']); //Initialisation de la variable erreurs 
    $pattern = "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/"; // Vérification du format de la date
    if (preg_match($pattern, $enchere_periode_activite_debut)) {
        $this->enchere_periode_activite_debut = $enchere_periode_activite_debut;
        if (isset($this->enchere_periode_activite_fin) && $this->enchere_periode_activite_fin <= $enchere_periode_activite_debut) {
            $this->erreurs['enchere_periode_activite_fin'] = "La date de fin d'enchère doit être supérieure à la date de début d'enchère.";
        }
    } else {
      $this->erreurs['enchere_periode_activite_debut'] = "Le date de début d'enchère doit être au format AAAA-MM-JJ.";
    }
  }

  /**
   * Mutateur de la propriété enchere_prix_plancher 
   * @param string $enchere_prix_plancher
   * @return $this
   */    
  public function setEnchere_prix_plancher($enchere_prix_plancher) {
    unset($this->erreurs['enchere_prix_plancher']); //Initialisation de la variable erreurs 
    $pattern = "/^[0-9]+.[0-9]+$/"; // Vérification du format du prix
    if (preg_match($pattern, $enchere_prix_plancher)) {
        $this->enchere_prix_plancher = $enchere_prix_plancher;
    } else {
      $this->erreurs['enchere_prix_plancher'] = "Le prix plancher doit être inscrit au format 00.00.";
    }
  }

  /**
   * Mutateur de la propriété enchere_quantite_mises
   * @param string $enchere_quantite_mises
   * @return $this
   */    
  public function setEnchere_quantite_mises($enchere_quantite_mises) {
    unset($this->erreurs['enchere_quantite_mises']); //Initialisation de la variable erreurs 
    $pattern = "/^[0-9][0-9]*$/"; // Vérification du format de la quantité de mises
    if (preg_match($pattern, $enchere_quantite_mises)) {
        $this->enchere_quantite_mises = $enchere_quantite_mises;
    } else {
      $this->erreurs['enchere_quantite_mises'] = "Seulement le format numérique est accepté.";
    }
  }

  /**
   * Mutateur de la propriété enchere_cdc_lord
   * @param string $enchere_cdc_lord
   * @return $this
   */    
  public function setEnchere_cdc_lord($enchere_cdc_lord) {
    unset($this->erreurs['enchere_cdc_lord']); //Initialisation de la variable erreurs 
    if($enchere_cdc_lord == 0 || $enchere_cdc_lord == 1){
        $this->enchere_cdc_lord = $enchere_cdc_lord;
    } else {
      $this->erreurs['enchere_cdc_lord'] = "Seulement les valeurs '0' ou '1' sont acceptées.";
    }
  }

  /**
   * Mutateur de la propriété enchere_periode_activite_fin
   * @param string $enchere_periode_activite_fin
   * @return $this
   */    
  public function setEnchere_periode_activite_fin($enchere_periode_activite_fin) {
    unset($this->erreurs['enchere_periode_activite_fin']); //Initialisation de la variable erreurs 
    $pattern = "/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/"; // Vérification du format de la date
    if (preg_match($pattern, $enchere_periode_activite_fin)) {
        $this->enchere_periode_activite_fin= $enchere_periode_activite_fin;
        if (isset($this->enchere_periode_activite_debut) && $this->enchere_periode_activite_debut >= $enchere_periode_activite_fin) {
            $this->erreurs['enchere_periode_activite_fin'] = "La date de fin d'enchère doit être supérieure à la date de début d'enchère.";
        }
    } else {
      $this->erreurs['enchere_periode_activite_fin'] = "Le date de fin d'enchère doit être au format AAAA-MM-JJ.";
    }
  }

  /**
   * Mutateur de la propriété enchere_offre_actuelle_prix 
   * @param string $enchere_offre_actuelle_prix
   * @return $this
   */    
    public function setEnchere_offre_actuelle_prix($enchere_offre_actuelle_prix) {
        unset($this->erreurs['enchere_offre_actuelle_prix']); //Initialisation de la variable erreurs 
        $enchere_offre_actuelle_prix = ($enchere_offre_actuelle_prix === '') ? null : $enchere_offre_actuelle_prix;
        $pattern = "/^[0-9]+(\.[0-9]{1,2})?$/"; // Vérification du format du prix
        if ($enchere_offre_actuelle_prix === null || preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $enchere_offre_actuelle_prix)) {
          $this->enchere_offre_actuelle_prix = $enchere_offre_actuelle_prix;
        } else {
            $this->erreurs['enchere_offre_actuelle_prix'] = "Le prix de l'offre actuelle doit être inscrit au format 00.00.";
        }
    }

    /**
   * Mutateur de la propriété enchere_offre_actuelle_nom_membre 
   * @param string $enchere_offre_actuelle_nom_membre
   * @return $this
   */    
    public function setEnchere_offre_actuelle_nom_membre($enchere_offre_actuelle_nom_membre) {
        unset($this->erreurs['enchere_offre_actuelle_nom_membre']); //Initialisation de la variable erreurs 
        $enchere_offre_actuelle_nom_membre = ($enchere_offre_actuelle_nom_membre === '') ? null : $enchere_offre_actuelle_nom_membre;
        $pattern = "/^[a-zA-ZÀ-ÖØ-öø-ÿ]{2,}(?:\s[a-zA-ZÀ-ÖØ-öø-ÿ]{2,})*$/"; // Vérification du format du nom
        if ($enchere_offre_actuelle_nom_membre === null || preg_match("/^[a-zA-ZÀ-ÖØ-öø-ÿ]{2,}(?:\s[a-zA-ZÀ-ÖØ-öø-ÿ]{2,})*$/", $enchere_offre_actuelle_nom_membre)) {
          $this->enchere_offre_actuelle_nom_membre = $enchere_offre_actuelle_nom_membre;
        } else {
            $this->erreurs['enchere_offre_actuelle_nom_membre'] = "Le nom doit contenir au moins deux caractères alphabétiques par mot.";
        }
    }

    /**
   * Mutateur de la propriété enchere_utilisateur_id
   * @param string $enchere_utilisateur_id
   * @return $this
   */    
  public function setEnchere_utilisateur_id($enchere_utilisateur_id) {
    if (preg_match('/^[1-9][0-9]*$/', $enchere_utilisateur_id)) {
        $this->enchere_utilisateur_id = $enchere_utilisateur_id;
    } else {
        $this->erreurs['enchere_utilisateur_id'] = "L'id de l'utilisateur doit être une valeur entière strictement positive.";
    }
  }
}

