<?php

/**
 * Classe de l'entité Utilisateur
 *
 */
class Utilisateur extends Entite
{
  protected $utilisateur_id = 0;
  protected $utilisateur_nom;
  protected $utilisateur_prenom;
  protected $utilisateur_courriel;
  protected $utilisateur_mdp;
  protected $utilisateur_profil_id = 0; 
  protected $utilisateur_statut = "oui"; 
  protected $messageErreur; 

  const ADMINISTRATEUR        = 1;
  const MEMBRE                = 0;
  const ERR_COURRIEL_EXISTANT = "Courriel déjà utilisé.";

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getUtilisateur_id()           { return $this->utilisateur_id; }
  public function getUtilisateur_nom()          { return $this->utilisateur_nom; }
  public function getUtilisateur_prenom()       { return $this->utilisateur_prenom; }
  public function getUtilisateur_courriel()     { return $this->utilisateur_courriel; }
  public function getUtilisateur_mdp()          { return $this->utilisateur_mdp; }
  public function getUtilisateur_profil_id()    { return $this->utilisateur_profil_id; }
  public function getUtilisateur_statut()       { return $this->utilisateur_statut; }
  public function getErreurs()                  { return $this->erreurs; }

  /**
   * Mutateur de la propriété utilisateur_id 
   * @param int $utilisateur_id
   * @return $this
   */    
  public function setUtilisateur_id($utilisateur_id) {
    if (preg_match('/^[1-9]\d*$/', $utilisateur_id)) {
        $this->utilisateur_id = $utilisateur_id;
    } else {
        $this->erreurs['utilisateur_id'] = "L'id de l'utilisateur doit être une valeur entière strictement positive.";
    }
  }

  /**
   * Mutateur de la propriété utilisateur_nom 
   * @param string $utilisateur_nom
   * @return $this
   */    
  public function setUtilisateur_nom($utilisateur_nom) {
    unset($this->erreurs['utilisateur_nom']); //Initialisation de la variable erreurs 
    $pattern = "/^[a-zA-Z]{2,}(-[a-zA-Z]{2,})*$/"; // Vérification du format du nom
    if (preg_match($pattern, $utilisateur_nom)) {
        $this->utilisateur_nom = $utilisateur_nom;
    } else {
      $this->erreurs['utilisateur_nom'] = "Le nom doit contenir au moins deux caractères alphabétiques par mot.";
    }
  }

  /**
   * Mutateur de la propriété utilisateur_prenom 
   * @param string $utilisateur_prenom
   * @return $this
   */    
  public function setUtilisateur_prenom($utilisateur_prenom) {
    unset($this->erreurs['utilisateur_prenom']); //Initialisation de la variable erreurs 
    $pattern = "/^[a-zA-Z]{2,}(-[a-zA-Z]{2,})*$/"; // Vérification du format du prénom
    if (preg_match($pattern, $utilisateur_prenom)) {
        $this->utilisateur_prenom = $utilisateur_prenom;
    } else {
      $this->erreurs['utilisateur_prenom'] = "Le prénom doit contenir au moins deux caractères alphabétiques par mot.";
    }
  }

  /**
   * Mutateur de la propriété utilisateur_courriel
   * @param string $utilisateur_courriel
   * @return $this
   */    
  public function setUtilisateur_courriel($utilisateur_courriel) {
    unset($this->erreurs['utilisateur_courriel']); //Initialisation de la variable erreurs 
    $pattern = "/^[\w\.-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z]{2,})+$/"; // Vérification du format du courriel
    if (preg_match($pattern, $utilisateur_courriel)) {
        $this->utilisateur_courriel = $utilisateur_courriel;
    } else {
      $this->erreurs['utilisateur_courriel'] = "Format incorrect.";
    }
  }

  /**
   * Mutateur de la propriété utilisateur_mdp
   * @param string $utilisateur_mdp
   * @return $this
   */    
  public function setUtilisateur_mdp($utilisateur_mdp) {
    unset($this->erreurs['utilisateur_mdp']); //Initialisation de la variable erreurs 
    $pattern = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*[%!:=]).{10,}$/"; // Vérification du format du mdp
    if (preg_match($pattern, $utilisateur_mdp)) {
        $this->utilisateur_mdp = $utilisateur_mdp;
    } else {
      $this->erreurs['utilisateur_mdp'] = "Le mot de passe doit contenir au moins 10 caractères, un caractère parmi %!:=, une majuscule, une minuscule et un chiffre.";
    }
  }

  /**
   * Mutateur de la propriété utilisateur_statut
   * @param string $utilisateur_statut
   * @return $this
   */    
  public function setutilisateur_statut($utilisateur_statut) {
    $statut_acceptes = ["oui", "non"];
    $utilisateur_statut = trim(strtolower($utilisateur_statut));
    // Vérification d'un profil d'utilisateur valide
    if (in_array($utilisateur_statut, $statut_acceptes)) {
        $this->utilisateur_statut = $utilisateur_statut;
    } else {
        $this->erreurs['utilisateur_statut'] = "Le statut sélectionné n'est pas valide.";
    }
  }

  /**
   * Mutateur de la propriété utilisateur_profil_id
   * @param string $utilisateur_profil_id
   * @return $this
   */    
  public function setUtilisateur_profil($utilisateur_profil) {
    $profil_acceptes = [        
        self::ADMINISTRATEUR,
        self::MEMBRE
    ];
    $utilisateur_profil = trim(strtolower($utilisateur_profil));
    // Vérification d'un profil d'utilisateur valide
    if (in_array($utilisateur_profil, $profil_acceptes)) {
        $this->utilisateur_profil = $utilisateur_profil;
    } else {
        $this->erreurs['utilisateur_profil'] = "Le profil sélectionné n'est pas valide.";
    }
  }

    /**
   * Mutateur de la propriété utilisateur_profil_id
   * @param string $utilisateur_profil_id
   * @return $this
   */    
  public function setUtilisateur_profil_id($utilisateur_profil_id) {
    $profil_acceptes = [        
        self::ADMINISTRATEUR,
        self::MEMBRE
    ];
    // Vérification d'un profil d'utilisateur valide
    if (in_array($utilisateur_profil_id, $profil_acceptes)) {
        $this->utilisateur_profil_id = $utilisateur_profil_id;
    } else {
        $this->erreurs['utilisateur_profil_id'] = "Le profil sélectionné n'est pas valide.";
    }
  }
}

