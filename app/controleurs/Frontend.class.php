<?php

/**
 * Classe Contrôleur des requêtes de l'application frontend
 */
class Frontend extends Routeur {  

    private $entite;
    private $action;
    private $timbre_id;
    private $timbres; 
    private $enchere_id; 

    private $messageRetourAction = "";
    private $methodes = [
      'devenirMembre' => [
        'cc'   => ['methode' => 'creerCompte'],
      ],
      'membre' => [
        'acm'  => ['methode' => 'afficherCompteMembre'],
        'aev'  => ['methode' => 'ajouterEnchereVue'],
        'ae'   => ['methode' => 'ajouterEnchere'],
        'at'   => ['methode' => 'ajouterTimbre'], 
        'aepr' => ['methode' => 'afficherEncheresPresentesUtil'],
        'aep'  => ['methode' => 'afficherEncheresPasseesUtil'],
        'conn' => ['methode' => 'connecter'],
        'm'    => ['methode' => 'afficherMise'],
        'me'   => ['methode' => 'miserEnchere'],
        'amu'  => ['methode' => 'afficherMisesUtil'],
        'ame'  => ['methode' => 'afficherModifierEnchere'],
        'moe'  => ['methode' => 'modifierEnchere'],
        'se'   => ['methode' => 'supprimerEnchere']
      ],
      'encheres' => [
        'c'   => ['methode'  => 'listerEncheres'],
        'cpr' => ['methode'  => 'listerEncheresPresentes'],
        'cpa' => ['methode'  => 'listerEncheresPassees']
      ]
    ];
    
  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */   
  public function __construct() {
    $this->entite    = $_GET['entite']        ?? 'devenirMembre';
    $this->action    = $_GET['action']        ?? 'cc';
    $this->oUtilConn = $_SESSION['oUtilConn'] ?? null; 
    $this->timbre_id = $_GET['timbre_id']     ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'espace membre
   */  
  public function gererMembre() {
    if (isset($_SESSION['oUtilConn'])) {
      $this->oUtilConn = $_SESSION['oUtilConn'];
        if (isset($this->methodes[$this->entite])) {
          if (isset($this->methodes[$this->entite][$this->action])) {
            $methode = $this->methodes[$this->entite][$this->action]['methode'];
            $this->$methode();
          } else {
            throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
          }
        } else {
          throw new Exception("L'entité $this->entite n'existe pas.");
        }
      } else {
      $this->creerCompte();
    }
  }

  /**
   * Gérer l'interface du catalogue
   */  
  public function gererCatalogue() {
    if (isset($_SESSION['oUtilConn'])) {
      $this->oUtilConn = $_SESSION['oUtilConn'];
    }
    if (isset($this->methodes[$this->entite])) {
      if (isset($this->methodes[$this->entite][$this->action])) {
        $methode = $this->methodes[$this->entite][$this->action]['methode'];
        $this->$methode();
      } else {
        throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
      }
    } else {
      throw new Exception("L'entité $this->entite n'existe pas.");
    }
  }

// **************************** CONNEXION / DÉCONNEXION **************************** //

  /**
   * Connecter un utilisateur et affichage de la page de connexion
   */  
  public function connecter() {
    $messageRetourAction = ''; 
    $utilisateur = []; 
    if (isset($_POST['utilisateur_courriel']) && isset($_POST['utilisateur_mdp'])) {
      if (!empty($_POST['utilisateur_courriel']) && !empty($_POST['utilisateur_mdp'])) {
        $utilisateur = $this->oRequetesSQL->connecter($_POST);
        if ($utilisateur !== false) {
          if($utilisateur['utilisateur_statut'] == "oui"){
          $_SESSION['oUtilConn'] = new Utilisateur($utilisateur);
          $this->oUtilConn = $_SESSION['oUtilConn']; 
          } else {
            $messageRetourAction = "Votre compte a été suspendu, veuillez contacter l'administration.";
          }
        } else {
          $messageRetourAction = "Courriel ou mot de passe incorrect.";
        }
      } else {
        $messageRetourAction = "Veuillez entrer vos informations.";
      }
    } else {
      $messageRetourAction = "Les champs courriel et mot de passe sont requis.";
    }
  
    if (!empty($messageRetourAction)){
      new Vue(
        'vConnexion',
        array(
            'titre'               => "Connexion",
            'messageRetourAction' => $messageRetourAction
        ),
        'gabarits/gabarit-frontend'
      );
    } else{
      new Vue(
        'vCompte',
        array(
            'titre'     => "Espace membre",
            'oUtilConn'   => $this->oUtilConn,
        ),
        'gabarits/gabarit-membre'
      );
    }
  }

  /**
   * Déconnecter un utilisateur et retour à la page d'accueil
   */
  public function deconnecter() {
    unset ($_SESSION['oUtilConn']);
    $this->oUtilConn = ""; 
    new Vue(
      'vIndex',
      array(
          'titre'     => "Stampee",
          'oUtilConn' => $this->oUtilConn
      ),
      'gabarits/gabarit-frontend'
    );
  }

// **************************** AFFICHAGES **************************** //

  /**
   * Afficher la page d'accueil
   */  
  public function afficherIndex() {
    $timbres = []; 

    new Vue(
        'vIndex',
        array(
            'titre'     => "Stampee",
            'oUtilConn' => $this->oUtilConn,
            'timbres'   => $this->oRequetesSQL->listerTimbresCoupCoeur()
        ),
        'gabarits/gabarit-frontend'
    );
  }

  /**
   * Afficher la page catalogue
   */  
  public function listerEncheres() {
    new Vue(
        'vCatalogue',
        array(
            'titre'     => "Toutes les enchères",
            'oUtilConn' => $this->oUtilConn,
            'timbres'   => $this->oRequetesSQL->listerEncheres()
        ),
        'gabarits/gabarit-frontend'
    );
  }

  /**
   * Afficher toutes les enchères en cours seulement 
   */  
  public function listerEncheresPresentes() {
    new Vue(
        'vCatalogue',
        array(
            'titre'     => "Enchères en cours",
            'oUtilConn' => $this->oUtilConn,
            'timbres'   => $this->oRequetesSQL->listerEncheresPresentes()
        ),
        'gabarits/gabarit-frontend'
    );
  }

  /**
   * Afficher toutes les enchères passées seulement 
   */  
  public function listerEncheresPassees() {
    new Vue(
        'vCatalogue',
        array(
            'titre'     => "Enchères passées",
            'oUtilConn' => $this->oUtilConn,
            'timbres'   => $this->oRequetesSQL->listerEncheresPassees()
        ),
        'gabarits/gabarit-frontend'
    );
  }

  /**
   * Voir les informations d'un timbre
   * 
   */  
  public function afficherTimbre() {
    $timbre = false;
    $this->timbre_id = $_GET['timbre_id'];
    if (!is_null($this->timbre_id)) {
      $timbre = $this->oRequetesSQL->getTimbre($this->timbre_id);
      if($timbre['enchere_cdc_lord'] == 1){
        $timbres = $this->oRequetesSQL->listerTimbresCoupCoeur(); 
      } else{
        $timbres = $this->oRequetesSQL->listerTimbresNonCoupCoeur(); 
      }
    }
    if (!$timbre) throw new Exception("Timbre inexistant.");
    new Vue("vTimbre",
            array(
              'titre'        => $timbre['timbre_nom'],
              'timbre'       => $timbre,
              'timbres'      => $timbres
            ),
            "gabarits/gabarit-frontend");
  }

  /**
   * Afficher la page de compte membre
   */  
  public function afficherCompteMembre() {
    new Vue(
      'vCompte',
      array(
        'oUtilConn'   => $this->oUtilConn,
        'titre'       => 'Espace membre',
        'messageRetourAction' => $this->messageRetourAction 
      ),
      'gabarits/gabarit-membre'
    );
  }

  /**
   * Afficher la page d'ajout d'enchère
   */  
  public function ajouterEnchereVue() {
    new Vue(
        'vAjouterEnchere',
        array(
            'titre'     => "Ajout d'enchère",
            'oUtilConn' => $this->oUtilConn,
            'messageRetourAction' => $this->messageRetourAction 
        ),
        'gabarits/gabarit-membre'
    );
  }

  /**
   * Afficher les enchères présentes d'un utilisateur en particulier
   */  
  public function afficherEncheresPresentesUtil() {
    new Vue(
        'vEncheresUtil',
        array(
            'titre'     => "Mes enchères présentes",
            'oUtilConn' => $this->oUtilConn,
            'timbres'    => $this->oRequetesSQL->listerEncheresUtil($this->oUtilConn->utilisateur_id),
        ),
        'gabarits/gabarit-membre'
    );
  }

  /**
   * Afficher les enchères passées d'un utilisateur en particulier
   */  
  public function afficherEncheresPasseesUtil() {
    new Vue(
        'vEncheresUtil',
        array(
            'titre'     => "Mes enchères passées",
            'oUtilConn' => $this->oUtilConn,
            'timbres'    => $this->oRequetesSQL->listerEncheresPasseesUtil($this->oUtilConn->utilisateur_id),
        ),
        'gabarits/gabarit-membre'
    );
  }

  /**
   * Afficher les mises d'un utilisateur en particulier
   */  
  public function afficherMisesUtil() {
    $utilisateur_nom = $this->oUtilConn->utilisateur_prenom . ' ' . $this->oUtilConn->utilisateur_nom;
    new Vue(
        'vEncheresUtil',
        array(
            'titre'     => "Mes mises",
            'oUtilConn' => $this->oUtilConn,
            'timbres'    => $this->oRequetesSQL->listerMisesUtil($utilisateur_nom),
        ),
        'gabarits/gabarit-membre'
    );
  }

  /**
   * Affichage du formulaire de modification d'une enchère
   */  
  public function afficherModifierEnchere() {
    new Vue(
        'vModifierEnchere',
        array(
            'titre'     => "Modification d'enchere",
            'oUtilConn' => $this->oUtilConn,
            'enchere'    => $this->oRequetesSQL->getEnchere($_GET['enchere_id'])
        ),
        'gabarits/gabarit-membre'
    );
  }

  /**
   * Afficher le formulaire de mise sur une enchère en particulier 
   */  
  public function afficherMise() {
    new Vue(
        'vMiser',
        array(
            'titre'     => "Miser",
            'oUtilConn' => $this->oUtilConn,
            'enchere'    => $this->oRequetesSQL->getEnchere($_GET['enchere_id'])
        ),
        'gabarits/gabarit-membre'
    );
  }


// **************************** CREATION DE COMPTE **************************** //

  /**
   * Créer un compte membre
   */
  public function creerCompte() {
    $utilisateur  = [];
    $erreurs = [];
    unset ($_SESSION['oUtilConn']);
    if (count($_POST) !== 0) {
      // retour de saisie du formulaire
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur); // création d'un objet Utilisateur pour contrôler la saisie
      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) { // aucune erreur de saisie -> requête SQL d'ajout
      $mdpCrypte = hash('sha512', $oUtilisateur->utilisateur_mdp);
        $utilisateur_id = $this->oRequetesSQL->ajouterUtilisateur([
          'utilisateur_prenom' => $oUtilisateur->utilisateur_prenom, 
          'utilisateur_nom'    => $oUtilisateur->utilisateur_nom,
          'utilisateur_courriel' => $oUtilisateur->utilisateur_courriel, 
          'utilisateur_mdp' => $mdpCrypte, 
          'utilisateur_profil_id' => $oUtilisateur->utilisateur_profil_id,
          'utilisateur_statut' => $oUtilisateur->utilisateur_statut, 
        ]);
        if ( $utilisateur_id > 0) { // test de la clé de l'utilisateur ajouté
          $this->messageRetourAction = "Votre compte a été créé avec succès. Vous pouvez désormais vous connecter.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Impossible de créer votre compte, veuillez réssayer.";
        }
        // exit;
      }
    }
    
    new Vue(
      'vCreationCompte',
          array(
            'oUtilConn'   => $this->oUtilConn,
            'titre'       => 'Espace membre',
            'erreurs'     => $erreurs,
            'messageRetourAction' => $this->messageRetourAction 
          ),
      'gabarits/gabarit-frontend');
  }

// **************************** AJOUT/MODIFICATION D'ENCHERE **************************** //

public function ajouterEnchere() {
  $erreursEnchere = [];
  $erreursTimbre = [];
  $erreursImage = [];
  $repertoire_images = '';

// Validation des champs de l'enchere
  $enchereData = [];
  foreach ($_POST as $name => $value) {
    if (strpos($name, "enchere_") === 0) {
      $enchereData[$name] = $value;
    }
  }
  $oEnchere = new Enchere($enchereData); 
  $erreursEnchere = $oEnchere->erreurs;

  if (count($erreursEnchere) === 0) {
   // Validation des champs du timbre
    $timbreData = [];
    foreach ($_POST as $name => $value) {
      if (strpos($name, "timbre_") === 0) {
        $timbreData[$name] = $value;
      }
    }
    $oTimbre = new Timbre($timbreData); 
    $erreursTimbre = $oTimbre->erreurs;

    if (count($erreursTimbre) === 0) {
      // Validation des champs de l'image
      $imageData = [];
      foreach ($_POST as $name => $value) {
        if (strpos($name, "image_") === 0) {
          $imageData[$name] = $value;
        }
      }
      $oImage = new Image($imageData); 
      $repertoire_images = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/';
      $image_fichier_temporaire = $_FILES['image_fichier']['tmp_name'];
      $image_nom_fichier = $_FILES['image_fichier']['name'];
      $chemin_nouveau_fichier = $repertoire_images . $image_nom_fichier;
      // if (move_uploaded_file($image_fichier_temporaire, $chemin_nouveau_fichier)) {
      //   echo("Image ajoutée avec succès"); 
      // } else {
      //   $erreursImage = "Erreur lors du téléchargement du fichier.";
      //   echo("Erreur lors du téléchargement du fichier."); 
      // }
      $oImage->setImage_fichier($_FILES['image_fichier']['name']);
      $erreursImage = $oImage->erreurs;

      if (count($erreursImage) === 0) {
        $enchere_id = $this->oRequetesSQL->ajouterEnchere([
          'enchere_periode_activite_debut'    => $oEnchere->enchere_periode_activite_debut, 
          'enchere_prix_plancher'             => $oEnchere->enchere_prix_plancher,
          'enchere_quantite_mises'            => $oEnchere->enchere_quantite_mises,
          'enchere_cdc_lord'                  => $oEnchere->enchere_cdc_lord,
          'enchere_periode_activite_fin'      => $oEnchere->enchere_periode_activite_fin,
          'enchere_offre_actuelle_prix'       => $oEnchere->enchere_offre_actuelle_prix,
          'enchere_offre_actuelle_nom_membre' => $oEnchere->enchere_offre_actuelle_nom_membre,
          'enchere_utilisateur_id'            => $oEnchere->enchere_utilisateur_id
        ]);           
        $timbre_id = $this->oRequetesSQL->ajouterTimbre([
          'timbre_nom'            => $oTimbre->timbre_nom, 
          'timbre_date_creation'  => $oTimbre->timbre_date_creation, 
          'timbre_pays'           => $oTimbre->timbre_pays, 
          'timbre_condition'      => $oTimbre->timbre_condition,
          'timbre_dimensions'     => $oTimbre->timbre_dimensions,
          'timbre_certification'  => $oTimbre->timbre_certification,
          'timbre_tirage'         => $oTimbre->timbre_tirage,
          'timbre_couleurs'       => $oTimbre->timbre_couleurs,
          'timbre_enchere_id'     => $enchere_id,
          'timbre_utilisateur_id' => $oTimbre->timbre_utilisateur_id
        ]);
        $image_id = $this->oRequetesSQL->ajouterImage([
          'image_timbre_id' => $timbre_id, 
          'image_type'      => $oImage->image_type, 
          'image_fichier'   => $oImage->image_fichier
        ]);

        $this->messageRetourAction = "L'enchère a été ajoutée avec succès.";
      } else {
        $this->classRetour = "erreur";
        $this->messageRetourAction = "Impossible d'ajouter l'image.";
      }
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Impossible d'ajouter le timbre.";
    }
  } else {
    $this->classRetour = "erreur";
    $this->messageRetourAction = "Impossible d'ajouter l'enchère.";
  }
  new Vue(
    'vAjouterEnchere',
    array(
        'titre'               => "Ajout d'enchère",
        'oUtilConn'           => $this->oUtilConn,
        'messageRetourAction' => $this->messageRetourAction,
        'erreursEnchere'      => $erreursEnchere,
        'erreursTimbre'       => $erreursTimbre,
        'erreursImage'        => $erreursImage,
        'image_fichier'       => $repertoire_images
    ),
    'gabarits/gabarit-membre'
  );
  exit;
}

public function modifierEnchere() {
  $erreursEnchere = [];
  $erreursTimbre = [];
  $repertoire_images = '';
  $enchere_id = $_GET['enchere_id'];
  $timbre_id = $_GET['timbre_id']; 

// Validation des champs de l'enchere
  $enchereData = [];
  foreach ($_POST as $name => $value) {
    if (strpos($name, "enchere_") === 0) {
      $enchereData[$name] = $value;
    }
  }
  $oEnchere = new Enchere($enchereData); 
  $erreursEnchere = $oEnchere->erreurs;

  if (count($erreursEnchere) === 0) {
   // Validation des champs du timbre
    $timbreData = [];
    foreach ($_POST as $name => $value) {
      if (strpos($name, "timbre_") === 0) {
        $timbreData[$name] = $value;
      }
    }
    print_r($timbreData);
    $oTimbre = new Timbre($timbreData); 
    $erreursTimbre = $oTimbre->erreurs;
    print_r($erreursTimbre);
    print_r($oTimbre);

    if (count($erreursTimbre) === 0) {
        $enchere_id = $this->oRequetesSQL->ajouterEnchere([
          'enchere_periode_activite_debut'    => $oEnchere->enchere_periode_activite_debut, 
          'enchere_prix_plancher'             => $oEnchere->enchere_prix_plancher,
          'enchere_quantite_mises'            => $oEnchere->enchere_quantite_mises,
          'enchere_cdc_lord'                  => $oEnchere->enchere_cdc_lord,
          'enchere_periode_activite_fin'      => $oEnchere->enchere_periode_activite_fin,
          'enchere_offre_actuelle_prix'       => $oEnchere->enchere_offre_actuelle_prix,
          'enchere_offre_actuelle_nom_membre' => $oEnchere->enchere_offre_actuelle_nom_membre,
          'enchere_utilisateur_id'            => $oEnchere->enchere_utilisateur_id
        ]);           
        $timbre_id = $this->oRequetesSQL->ajouterTimbre([
          'timbre_nom'            => $timbre_id, 
          'timbre_date_creation'  => $oTimbre->timbre_date_creation, 
          'timbre_pays'           => $oTimbre->timbre_pays, 
          'timbre_condition'      => $oTimbre->timbre_condition,
          'timbre_dimensions'     => $oTimbre->timbre_dimensions,
          'timbre_certification'  => $oTimbre->timbre_certification,
          'timbre_tirage'         => $oTimbre->timbre_tirage,
          'timbre_couleurs'       => $oTimbre->timbre_couleurs,
          'timbre_enchere_id'     => $enchere_id,
          'timbre_utilisateur_id' => $oTimbre->timbre_utilisateur_id
        ]);

        $this->messageRetourAction = "L'enchère a été modifiée avec succès.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Impossible de modifier le timbre.";
    }
  } else {
    $this->classRetour = "erreur";
    $this->messageRetourAction = "Impossible de modifier l'enchère.";
  }
      new Vue('vModifierEnchere',
        array(
          'titre'               => "Modifier l'enchère numéro $enchere_id",
          'oUtilConn'           => $this->oUtilConn,
          'messageRetourAction' => $this->messageRetourAction,
          'erreursEnchere'      => $erreursEnchere,
          'erreursTimbre'       => $erreursTimbre,
          'image_fichier'       => $repertoire_images,
          'enchere'             => $this->oRequetesSQL->getEnchere($_GET['enchere_id'])
        ),
        'gabarits/gabarit-frontend');
  exit;
}

  /**
   * Supprimer une enchère
   */  
  public function supprimerEnchere() {
  $utilisateur_id = $_GET['id'];
  $enchere_utilisateur_id = $_POST['enchere_utilisateur_id'];
  if($utilisateur_id == $enchere_utilisateur_id){
    $this->oRequetesSQL->supprimerEnchere($_GET['enchere_id']);
    $messageRetourAction = "Suppression de l'enchère effectuée.";
  }
  else{
    $messageRetourAction = "Vous ne pouvez supprimer que les enchères dont vous êtes le créateur.";
  }
    new Vue(
      'vEncheresUtil',
      array(
          'titre'               => "Mes enchères présentes",
          'oUtilConn'           => $this->oUtilConn,
          'timbres'             => $this->oRequetesSQL->listerEncheresUtil($this->oUtilConn->utilisateur_id),
          'messageRetourAction' => $messageRetourAction,
          ),
      'gabarits/gabarit-membre'
  );
  }


// **************************** MISE **************************** //
  
  /**
   * Faire une mise  
   */ 
  public function miserEnchere() {
    $erreursMise = "";
    $enchere_id = $_GET['enchere_id'];
    $ancienneMise = $this->oRequetesSQL->getEnchere($enchere_id);
    $messageRetourAction = ""; 
    
    if (count($_POST) !== 0) {
        $mise = $_POST;
        if ($ancienneMise !== false) {
            $enchere_offre_actuelle_prix = $ancienneMise['enchere_offre_actuelle_prix'];
            $enchere_prix_plancher = $ancienneMise['enchere_prix_plancher']; 
            if ($enchere_offre_actuelle_prix >= $mise['enchere_offre_actuelle_prix'] || $enchere_prix_plancher >= $mise['enchere_offre_actuelle_prix']) {
                $erreursMise = "Votre mise doit être plus élevée que la mise précédente et que le prix plancher.";
            }
        }
        if (empty($erreursMise)) {
            $oMise = new Enchere($mise);  

            if (empty($erreursMise)) {
                if ($this->oRequetesSQL->miserEnchere([
                    'enchere_id' => $oMise->enchere_id,
                    'enchere_offre_actuelle_prix'       => $oMise->enchere_offre_actuelle_prix, 
                    'enchere_offre_actuelle_nom_membre' => $oMise->enchere_offre_actuelle_nom_membre
                ])) {
                    $messageRetourAction = "Mise effectuée.";
                } else {
                    $messageRetourAction = "Mise non effectuée.";
                }
            } else {
                $messageRetourAction = "Erreurs dans la mise.";
            }
        } else {
            $messageRetourAction = "Mise non effectuée"; 
        }
    }
    new Vue(
        'vMiser',
        array(
            'titre'                => "Miser",
            'messageRetourAction'  => $messageRetourAction, 
            'oUtilConn'            => $this->oUtilConn,
            'enchere'              => $this->oRequetesSQL->getEnchere($enchere_id),
            'erreursMise'          => $erreursMise,
        ),
        'gabarits/gabarit-membre'
    );
  }

  }