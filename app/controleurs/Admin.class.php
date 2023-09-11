<?php

/**
 * Classe Contrôleur des requêtes de l'interface admin
 */
class Admin extends Routeur {

  private $entite;
  private $action;
  private $utilisateur_id;

  private $methodes = [
    'utilisateur' => [
      'l'  => ['methode' => 'listerUtilisateurs', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      'a'  => ['methode' => 'ajouterUtilisateur', 'droits' => [Utilisateur::ADMINISTRATEUR, Utilisateur::MEMBRE]],
      'm'  => ['methode' => 'modifierUtilisateur', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      'ss' => ['methode' => 'suspendreUtilisateur', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      's'  => ['methode' => 'supprimerUtilisateur', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      'd'  => ['methode' => 'deconnecter', 'droits' => [Utilisateur::ADMINISTRATEUR, Utilisateur::MEMBRE]],
      'c'  => ['methode' => 'connecter', 'droits' => [Utilisateur::ADMINISTRATEUR, Utilisateur::MEMBRE]]
    ],
    'encheres' => [
      'ale' => ['methode' => 'listerEncheres', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      'aame'=> ['methode' => 'afficherModifierEncheres', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      'ame' => ['methode' => 'modifierEncheres', 'droits' => [Utilisateur::ADMINISTRATEUR]],
      'ase' => ['methode' => 'supprimerEncheres', 'droits' => [Utilisateur::ADMINISTRATEUR]]
    ]
  ];

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */ 
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'utilisateur';
    $this->action    = $_GET['action']    ?? 'l';
    $this->utilisateur_id = $_GET['utilisateur_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;  
  }

  /**
   * Gérer l'interface d'administration 
   */  
  public function gererAdmin() {
    if (isset($_SESSION['oUtilConn'])) {
      $this->oUtilConn = $_SESSION['oUtilConn'];
        if (isset($this->methodes[$this->entite])) {
          if (isset($this->methodes[$this->entite][$this->action])) {
            $methode = $this->methodes[$this->entite][$this->action]['methode'];
            $droitsAutorises = $this->methodes[$this->entite][$this->action]['droits']; 
            $profilUtilisateur = $this->oUtilConn->getUtilisateur_profil_id();
            if (in_array($profilUtilisateur, $droitsAutorises)) {
                $this->$methode();
            } else {
                new Vue('erreurs/vErreur403', [
                  'oUtilConn' => $this->oUtilConn
                ], 'gabarits/gabarit-admin');
            }
          } else {
            throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
          }
        } else {
          throw new Exception("L'entité $this->entite n'existe pas.");
        }
      } else {
      $this->connecter();
    }
  }

  /**
   * Connecter un utilisateur
   */
  public function connecter() {
    $messageErreurConnexion = ""; 
    if (count($_POST) !== 0) {
      $utilisateur = $this->oRequetesSQL->connecter($_POST);
      if ($utilisateur !== false) {
        $_SESSION['oUtilConn'] = new Utilisateur($utilisateur);
        if ($_SESSION['oUtilConn']->utilisateur_statut == "oui")
        {
          $this->gererAdmin();
          exit;  
        } else{
          unset($_SESSION['oUtilConn']);
          $messageErreurConnexion = "Vote compte a été suspendu. Veuillez contacter l'administration.";
        }  
      } else { 
        $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
      }
    }
    
    new Vue('modaleConnexionAdmin',
            array(
              'titre'                  => 'Connexion',
              'messageErreurConnexion' => $messageErreurConnexion
            ),
            'gabarits/gabarit-admin-min');
  }

  /**
   * Déconnecter un utilisateur
   */
  public function deconnecter() {
    unset ($_SESSION['oUtilConn']);
    $this->connecter();
  }

  /**
   * Lister les utilisateurs
   */
  public function listerUtilisateurs() {
    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    new Vue('admin/vAdminUtilisateurs',
            array(
              'oUtilConn'           => $this->oUtilConn,
              'titre'               => 'Gestion des utilisateurs',
              'utilisateurs'        => $utilisateurs,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarits/gabarit-admin');
  }

  /**
   * Ajouter un utilisateur
   */
  public function ajouterUtilisateur() {
    $utilisateur  = [];
    $erreurs = [];
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
          $this->messageRetourAction = "Votre compte a été créé avec succès. Bienvenue!";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Impossible de créer votre compte.";
        }
        exit;
      }
    }
    
    new Vue('vCompte',
            array(
              'oUtilConn'   => $this->oUtilConn,
              'titre'       => 'Espace membre',
              'utilisateur' => $utilisateur,
              'erreurs'     => $erreurs
            ),
            'gabarits/gabarit-frontend');
  }

  /**
   * Supprimer un utilisateur
   */
  public function supprimerUtilisateur() {
    if ($this->oRequetesSQL->supprimerUtilisateur($this->utilisateur_id)) {
      $this->messageRetourAction = "Suppression de l'utilisateur numéro $this->utilisateur_id effectuée.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression de l'utilisateur numéro $this->utilisateur_id non effectuée.";
    }
    $this->listerUtilisateurs();
  }

  /**
   * Modifier un utilisateur identifié par sa clé dans la propriété utilisateur_id
   */
  public function modifierUtilisateur() {
    if (count($_POST) !== 0) {
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur);
      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) {
        if($this->oRequetesSQL->modifierUtilisateur([
          'utilisateur_id'        => $oUtilisateur->utilisateur_id,
          'utilisateur_nom'       => $oUtilisateur->utilisateur_nom,
          'utilisateur_prenom'    => $oUtilisateur->utilisateur_prenom, 
          'utilisateur_courriel'  => $oUtilisateur->utilisateur_courriel, 
          'utilisateur_profil_id' => $oUtilisateur->utilisateur_profil_id,
          'utilisateur_statut'    => $oUtilisateur->utilisateur_statut
        ])) {
          $this->messageRetourAction = "Modification de l'utilisateur numéro $this->utilisateur_id effectuée.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Modification de l'utilisateur numéro $this->utilisateur_id non effectuée.";
        }
        $this->listerUtilisateurs();
        exit;
      }

    } else {
      $utilisateur  = $this->oRequetesSQL->getUtilisateur($this->utilisateur_id);
      $erreurs = [];
    }
    
    new Vue('admin/vAdminUtilisateurModifier',
            array(
              'oUtilConn'   => $this->oUtilConn,
              'titre'       => "Modifier l'utilisateur numéro $this->utilisateur_id",
              'utilisateur' => $utilisateur,
              'erreurs'     => $erreurs
            ),
            'gabarits/gabarit-admin');
  }

  /**
   * Afficher toutes les enchères  
   */
  public function listerEncheres() {
  $encheres = $this->oRequetesSQL->listerEncheres();
  new Vue('admin/vAdminEncheres',
          array(
            'oUtilConn'           => $this->oUtilConn,
            'titre'               => 'Gestion des enchères',
            'encheres'            => $encheres,
            'messageRetourAction' => $this->messageRetourAction
          ),
          'gabarits/gabarit-admin');
  }

  /**
   * Afficher la modification d'enchère
   */
  public function afficherModifierEncheres(){
    $erreurs = ""; 
    $enchere_id = $_GET['enchere_id']; 
    $enchere = $this->oRequetesSQL->getEnchere($enchere_id);
    new Vue('admin/vAdminEncheresModifier',
            array(
              'oUtilConn'   => $this->oUtilConn,
              'titre'       => "Modifier l'enchère numéro $enchere_id",
              'enchere'     => $enchere,
              'erreurs'     => $erreurs
            ),
            'gabarits/gabarit-admin');
  }

  /**
   * Modifier une enchère
   */
  public function modifierEncheres() {
    $messageRetourAction = ""; 
    $enchere_id = $_GET['enchere_id'];
    $enchere = $this->oRequetesSQL->getEnchere($enchere_id);  
    $timbre = $enchere['timbre_nom'];
    if (count($_POST) !== 0) {
      $enchere = $_POST;

      if (empty($enchere['enchere_offre_actuelle_prix'])) {
        $enchere['enchere_offre_actuelle_prix'] = null;
      }
      if (empty($enchere['enchere_offre_actuelle_nom_membre'])) {
          $enchere['enchere_offre_actuelle_nom_membre'] = null;
      }

      $oEnchere = new Enchere($enchere);
      $erreurs = $oEnchere->erreurs;
      if (count($erreurs) === 0) {
        if($this->oRequetesSQL->modifierEncheres([
          'enchere_id'                        => $enchere_id,
          'enchere_periode_activite_debut'    => $oEnchere->enchere_periode_activite_debut,
          'enchere_prix_plancher'             => $oEnchere->enchere_prix_plancher, 
          'enchere_quantite_mises'            => $oEnchere->enchere_quantite_mises, 
          'enchere_cdc_lord'                  => $oEnchere->enchere_cdc_lord, 
          'enchere_periode_activite_fin'      => $oEnchere->enchere_periode_activite_fin, 
          'enchere_utilisateur_id'            => $oEnchere->enchere_utilisateur_id
        ])) {
          $enchere = $this->oRequetesSQL->getEnchere($oEnchere->enchere_id);
          $messageRetourAction = "Modification de l'enchère numéro $enchere_id effectuée.";
        } else {
          $messageRetourAction = "Modification de l'enchère numéro $enchere_id non effectuée.";

        }
      }
    } else {
      $erreurs = "";
    }
    
    new Vue('admin/vAdminEncheresModifier',
            array(
              'oUtilConn'           => $this->oUtilConn,
              'titre'               => "Modifier l'enchère numéro $enchere_id",
              'enchere'             => $enchere,
              'timbre'              => $timbre, 
              'erreurs'             => $erreurs,
              'messageRetourAction' => $messageRetourAction
            ),
            'gabarits/gabarit-admin');
  }

}
