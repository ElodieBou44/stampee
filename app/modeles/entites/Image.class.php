<?php

/**
 * Classe de l'entité Image
 *
 */
class Image extends Entite
{
  protected $image_id = 0;
  protected $image_fichier;
  protected $image_timbre_id;
  protected $image_type;
  protected $messageErreur; 

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getImage_id()               { return $this->image_id; }
  public function getImage_fichier()          { return $this->image_fichier; }
  public function getImage_timbre_id()        { return $this->image_timbre_id; }
  public function getImage_type()             { return $this->image_type; }
  public function getErreurs()                { return $this->erreurs; }

  /**
   * Mutateur de la propriété enchere_id 
   * @param int $enchere_id
   * @return $this
   */    
  public function setImage_id($image_id) {
    if (preg_match('/^[1-9]\d*$/', $image_id)) {
        $this->image_id = $image_id;
    } else {
        $this->erreurs['image_id'] = "L'id de l'image doit être une valeur entière strictement positive.";
    }
  }

  /**
   * Mutateur de la propriété image_fichier
   * @param string image_fichier
   * @return $this
   */    
  public function setImage_fichier($image_fichier) {
    unset($this->erreurs['image_fichier']); //Initialisation de la variable erreurs 
    $pattern = "/^.+\.(jpg|jpeg|png)$/"; // Vérification du format de l'image
    if (preg_match($pattern, $image_fichier)) {
        $this->image_fichier= $image_fichier;
    } else {
      $this->erreurs['image_fichier'] = "Seulement les images de type jpg, jpeg ou png sont acceptées.";
    }
  }

/**
   * Mutateur de la propriété image_type
   * @param string $image_type
   * @return $this
   */    
  public function setImage_type($image_type) {
    unset($this->erreurs['image_type']); //Initialisation de la variable erreurs 
    if($image_type == 'primaire' || $image_type == 'secondaire'){
        $this->image_type = $image_type;
    } else {
      $this->erreurs['image_type'] = "Seulement les valeurs primaire ou secondaire sont acceptées.";
    }
  }
  
}

