<?php

define("ENV", "DEV");
if (ENV === "DEV") define("MOCK_NOW", "2021-10-25");

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO {

//**************** GESTION DES UTILISATEURS ****************// 

  /**
   * Connecter un utilisateur
   * @param array $champs, tableau avec les champs utilisateur_courriel et utilisateur_mdp  
   * @return array|false ligne de la table, false sinon 
   */ 
  public function connecter($champs) {
    $this->sql = "
      SELECT * FROM utilisateur
      WHERE utilisateur_courriel = :utilisateur_courriel AND utilisateur_mdp = SHA2(:utilisateur_mdp, 512)";
    $this->params = $champs;
    return $this->getLignes(self::UNE_SEULE_LIGNE);
  } 

  /**
   * Récupération d'un utilisateur de la table utilisateur
   * @param int $utilisateur_id 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne
   */ 
  public function getUtilisateur($utilisateur_id) {
    $this->sql = '
      SELECT * FROM utilisateur WHERE utilisateur_id = :utilisateur_id';
    $this->params = ['utilisateur_id' => $utilisateur_id];
    return $this->getLignes(self::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des utilisateurs 
   * @return array tableau des utilisateurs 
   */

  public function getUtilisateurs(){
    $this->sql = "SELECT * FROM utilisateur";
    $this->params = [];  
    return $this->getLignes();
  }

  /**
   * Ajout d'un utilisateur
   * @param array $champs tableau des champs de l'utilisateur 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

  public function ajouterUtilisateur($champs){
    $this->sql = " INSERT INTO utilisateur SET utilisateur_prenom = :utilisateur_prenom, 
    utilisateur_nom = :utilisateur_nom, 
    utilisateur_courriel = :utilisateur_courriel, utilisateur_mdp = :utilisateur_mdp, 
    utilisateur_statut = :utilisateur_statut, utilisateur_profil_id = :utilisateur_profil_id ";
    $this->params = $champs;  
    return $this->CUDLigne();
  }

  /**
   * Modifier un utilisateur
   * @param array $champs tableau avec les champs à modifier et la clé utilisateur_id
   * @return boolean true si modification effectuée, false sinon
   */ 
  public function modifierUtilisateur($champs) {
    $this->sql = " UPDATE utilisateur SET 
      utilisateur_nom = :utilisateur_nom, 
      utilisateur_prenom = :utilisateur_prenom,
      utilisateur_courriel = :utilisateur_courriel, 
      utilisateur_profil_id = :utilisateur_profil_id,
      utilisateur_statut = :utilisateur_statut
        WHERE utilisateur_id = :utilisateur_id";
    $this->params = $champs;  
    return $this->CUDLigne();
  }

  /**
   * Supprimer un utilisateur
   * @param int $utilisateur_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */ 
  public function supprimerUtilisateur($utilisateur_id) {
    $this->sql = " DELETE FROM utilisateur
    WHERE utilisateur_id = :utilisateur_id
    AND utilisateur_profil_id != 1 ";
    $this->params = ['utilisateur_id' => $utilisateur_id];  
    return $this->CUDLigne(); 
  }

  //*************** GESTION DES TIMBRES / ENCHÈRES ***************// 

  //********* AFFICHAGE *********//

  /**
   * Affichage d'un timbre et l'enchère et les images liées 
   * @param array $champs tableau des champs de l'utilisateur 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

  public function getTimbre($timbre_id){
    $this->sql = " SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id,
    u.utilisateur_prenom, u.utilisateur_nom
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    LEFT JOIN `utilisateur` u ON u.utilisateur_id = t.timbre_utilisateur_id
    WHERE t.timbre_id = $timbre_id
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";

    return $this->getLignes(['timbre_id' => $timbre_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupérer une enchère, le timbre et les images liés 
   * @return array tableau contenant toutes les enchères
   */

  public function getEnchere($enchere_id){
    $this->params = array();
    $this->sql = " SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_id = $enchere_id
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";
    
    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Modifier une enchère lors d'une mise
   * @return array tableau contenant toutes les enchères
   */
  public function miserEnchere($champs){
    $this->sql = "UPDATE enchere SET 
    enchere_offre_actuelle_prix = :enchere_offre_actuelle_prix, 
    enchere_offre_actuelle_nom_membre = :enchere_offre_actuelle_nom_membre
    WHERE enchere_id = :enchere_id";
    
    $this->params = $champs;
    return $this->CUDLigne();
  }


  /**
   * Supprimer une enchère
   * @return array tableau contenant toutes les enchères
   */
  public function supprimerEnchere($enchere_id) {
    $this->sql = " DELETE FROM enchere
    WHERE enchere_id = :enchere_id ";
    $this->params = ['enchere_id' => $enchere_id];  
    return $this->CUDLigne(); 
  }

  /**
   * Modifier une enchère
   * @return array tableau contenant toutes les enchères
   */
  public function modifierEnchere($champs){
    $this->sql = " UPDATE enchere SET
      enchere_periode_activite_debut = :enchere_periode_activite_debut, 
      enchere_prix_plancher = :enchere_prix_plancher,
      enchere_quantite_mises = :enchere_quantite_mises,
      enchere_cdc_lord = :enchere_cdc_lord,
      enchere_periode_activite_fin = :enchere_periode_activite_fin,
      enchere_offre_actuelle_prix = :enchere_offre_actuelle_prix,
      enchere_offre_actuelle_nom_membre = :enchere_offre_actuelle_nom_membre,
      enchere_utilisateur_id = :enchere_utilisateur_id
    WHERE enchere_id = :enchere_id ";
    
    $this->params = $champs;
    return $this->CUDLigne();
  }

  /**
   * Modifier une enchère (admin)
   * @return array tableau contenant toutes les enchères
   */
  public function modifierEncheres($champs){
    $this->sql = " UPDATE enchere SET
      enchere_periode_activite_debut = :enchere_periode_activite_debut, 
      enchere_prix_plancher = :enchere_prix_plancher,
      enchere_quantite_mises = :enchere_quantite_mises,
      enchere_cdc_lord = :enchere_cdc_lord,
      enchere_periode_activite_fin = :enchere_periode_activite_fin,
      enchere_utilisateur_id = :enchere_utilisateur_id
    WHERE enchere_id = :enchere_id ";
    
    $this->params = $champs;
    return $this->CUDLigne();
  }

    /**
   * Modifier un timbre
   * @return array tableau contenant toutes les enchères
   */
  public function modifierTimbre($champs){
    $this->sql = "UPDATE timbre SET
      timbre_nom            = :timbre_nom, 
      timbre_date_creation  = :timbre_date_creation, 
      timbre_pays           = :timbre_pays, 
      timbre_condition      = :timbre_condition,
      timbre_dimensions     = :timbre_dimensions,
      timbre_certification  = :timbre_certification,
      timbre_tirage         = :timbre_tirage,
      timbre_couleurs       = :timbre_couleurs,
      timbre_enchere_id     = :timbre_enchere_id,
      timbre_utilisateur_id = :timbre_utilisateur_id 
    WHERE timbre_id = :timbre_id";
    
    $this->params = $champs;
    return $this->CUDLigne();
  }

  /**
   * Modifier une image
   * @return array tableau contenant toutes les enchères
   */
  public function modifierImage($champs){
    $this->sql = "UPDATE `image` SET
      image_timbre_id => :image_timbre_id, 
      image_type      => :image_type, 
      image_fichier   => :image_fichier
    WHERE image_id = :image_id";
    
    $this->params = $champs;
    return $this->CUDLigne();
  }
  /**
   * Affichage de tous les timbres et l'enchère et les images liées 
   * @return array tableau contenant toutes les enchères
   */

  public function listerEncheres(){
    $this->sql = " SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";
    $this->params = [];  
    return $this->getLignes();
  }

  /**
   * Affichage des timbres qui sont des coups de coeur du Lord 
   * @return array tableau contenant toutes les enchères
   */
  public function listerTimbresCoupCoeur()
  {
    $this->sql = "SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_cdc_lord = 1
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id";
    $this->params = [];  
    return $this->getLignes();
  }

  /**
   * Affichage des timbres qui ne sont pas des coups de coeur du Lord 
   * @return array tableau contenant toutes les enchères
   */
  public function listerTimbresNonCoupCoeur()
  {
    $this->sql = "SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_cdc_lord = 0
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id";
    $this->params = [];  
    return $this->getLignes();
  }

    /**
   * Affichage de tous les timbres et l'enchère et les images liées des enchères en cours
   * @return array tableau contenant toutes les enchères
   */

  public function listerEncheresPresentes(){
    $this->sql = " SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_periode_activite_fin > CURDATE()
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";
    $this->params = [];  
    return $this->getLignes();
  }

  /**
   * Affichage de tous les timbres et l'enchère et les images liées des enchères passées
   * @return array tableau contenant toutes les enchères
   */

  public function listerEncheresPassees(){
    $this->sql = " SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_periode_activite_fin < CURDATE()
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";
    $this->params = [];  
    return $this->getLignes();
  }

    /**
   * Affichage de tous les timbres et l'enchère et les images liées aux enchères courantes d'un utilisateur
   * @return array tableau contenant toutes les enchères
   */

  public function listerEncheresUtil($utilisateur_id) {
    $this->sql = "SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_periode_activite_fin > CURDATE() AND e.enchere_utilisateur_id = $utilisateur_id
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id";

    $this->params = [];  
    return $this->getLignes();
}


      /**
   * Affichage de tous les timbres et l'enchère et les images liées aux enchères courantes d'un utilisateur
   * @return array tableau contenant toutes les enchères
   */

  public function listerEncheresPasseesUtil($utilisateur_id){
    $this->sql = " SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_periode_activite_fin < CURDATE() AND e.enchere_utilisateur_id = $utilisateur_id
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";
    $this->params = [];  
    return $this->getLignes();
  }

    /**
   * Affichage de toutes les mises d'un utilisateur
   * @return array tableau contenant toutes les enchères
   */

  public function listerMisesUtil($utilisateur_nom) {
    $this->sql = "SELECT t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id,
    GROUP_CONCAT(CONCAT(i.image_fichier, ' (', i.image_type, ')')) AS fichiers_images,
    e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id AS enchere_utilisateur_id
    FROM timbre t
    LEFT JOIN `image` i ON i.image_timbre_id = t.timbre_id
    LEFT JOIN `enchere` e ON e.enchere_id = t.timbre_enchere_id
    WHERE e.enchere_offre_actuelle_nom_membre = :utilisateur_nom
    GROUP BY t.timbre_id, t.timbre_nom, t.timbre_date_creation, t.timbre_pays, t.timbre_condition, t.timbre_dimensions, t.timbre_certification, t.timbre_tirage, t.timbre_couleurs, t.timbre_enchere_id, t.timbre_utilisateur_id, e.enchere_id, e.enchere_periode_activite_debut, e.enchere_prix_plancher, e.enchere_quantite_mises, e.enchere_cdc_lord, e.enchere_periode_activite_fin, e.enchere_offre_actuelle_prix, e.enchere_offre_actuelle_nom_membre, e.enchere_utilisateur_id ";

    $this->params = ['utilisateur_nom' => $utilisateur_nom];
    return $this->getLignes();
}

  //********* ACTIONS *********//

  /**
   * Ajout d'une enchère
   * @param array $champs tableau des champs de l'enchère
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

  public function ajouterEnchere($champs){
    $this->sql = " INSERT INTO enchere SET 
    enchere_periode_activite_debut = :enchere_periode_activite_debut, 
    enchere_prix_plancher = :enchere_prix_plancher, 
    enchere_quantite_mises = :enchere_quantite_mises, 
    enchere_cdc_lord = :enchere_cdc_lord, 
    enchere_periode_activite_fin = :enchere_periode_activite_fin,
    enchere_offre_actuelle_prix = :enchere_offre_actuelle_prix,
    enchere_offre_actuelle_nom_membre = :enchere_offre_actuelle_nom_membre,
    enchere_utilisateur_id = :enchere_utilisateur_id ";
    $this->params = $champs;  
    return $this->CUDLigne();
  }

  /**
   * Ajout d'un timbre
   * @param array $champs tableau des champs du timbre 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */

  public function ajouterTimbre($champs){
    $this->sql = " INSERT INTO timbre SET 
    timbre_nom = :timbre_nom, 
    timbre_date_creation = :timbre_date_creation, 
    timbre_pays = :timbre_pays, 
    timbre_condition = :timbre_condition, 
    timbre_dimensions = :timbre_dimensions, 
    timbre_certification = :timbre_certification, 
    timbre_tirage = :timbre_tirage, 
    timbre_couleurs = :timbre_couleurs, 
    timbre_enchere_id = :timbre_enchere_id, 
    timbre_utilisateur_id = :timbre_utilisateur_id ";
    $this->params = $champs;  
    return $this->CUDLigne();
  }

  /**
   * Ajout d'une image
   * @param array $champs tableau des champs de l'image 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterImage($champs){
    $this->sql = " INSERT INTO `image` SET 
    image_fichier = :image_fichier,   
    image_type = :image_type,
    image_timbre_id = :image_timbre_id ";
    $this->params = $champs;  
    return $this->CUDLigne();
  }

  // /**
  //  * Affichage de toutes les enchères
  //  * @return array tableau contenant toutes les enchères
  //  */

  // public function listerEncheres(){
  //   $this->sql = " SELECT *
  //   FROM enchere
  //   JOIN timbre ON timbre.timbre_enchere_id = enchere.enchere_id
  //   JOIN `image` ON `image`.image_timbre_id = timbre.timbre_id ";
  //   $this->params = [];  
  //   return $this->getLignes();
  // }

}