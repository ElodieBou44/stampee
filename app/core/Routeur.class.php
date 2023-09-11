<?php

/**
 * Classe Routeur
 * analyse l'uri et exécute la méthode associée  
 */
class Routeur {

  protected $oRequetesSQL; // Objet RequetesSQL utilisé par tous les contrôleurs
  protected $oUtilConn; // Objet de l'utilisateur connecté

  // Routes contenant l'uri, le gabarit, ainsi que la méthode
  private $routes = [
    ['',              'Frontend', 'afficherIndex'],
    ['timbre',        'Frontend', 'afficherTimbre'],
    ['miser',         'Frontend', 'miserTimbre'],
    ['catalogue',     'Frontend', 'gererCatalogue'],
    ['admin',         'Admin',    'gererAdmin'],
    ['compte',        'Frontend', 'gererMembre'],
    ['connecter',     'Frontend', 'connecter'],
    ['deconnecter',   'Frontend', 'deconnecter']
  ];

  const BASE_URI = '/';

  const ERROR_FORBIDDEN = 'HTTP 403';
  const ERROR_NOT_FOUND = "HTTP 404";

  /**
   * Constructeur qui valide l'URI,
   * instancie un contrôleur et exécute une méthode de ce contrôleur,
   * chaque URI valide est associé à un contrôleur et une méthode de ce contrôleur
   */
  public function __construct() {
    try {

      $uri = $_SERVER['REQUEST_URI'];
      if (strpos($uri, '?')) $uri = strstr($uri, '?', true);

      foreach ($this->routes as $route) { // balayage du tableau des routes
 
        $routeUri     = self::BASE_URI.$route[0];
        $routeClasse  = $route[1];
        $routeMethode = $route[2];

        if ($uri === $routeUri) {
          // Exécution de la méthode associée à l'uri
          $oControleur = new $routeClasse;
          $oControleur->$routeMethode();
          exit;
        }
      }
      // lorsqu'aucune route ne correspond à l'uri
      throw new Exception(self::ERROR_NOT_FOUND);

    } catch(Error | Exception $e) {
      $this->erreur($e);
    }
  }

  /**
   * Méthode qui envoie un compte-rendu d'erreur
   * @param Exception $e 
   */
  public function erreur($e) {
    $message = $e->getMessage();
    if ($message == self::ERROR_NOT_FOUND) {
      header('HTTP/1.1 404 Not Found');
      new Vue('vErreur404', [], 'gabarit-erreur');
    } else if ($message == self::ERROR_FORBIDDEN) {
      header('HTTP/1.1 403 Forbidden');
    } else {
      header('HTTP/1.1 500 Internal Server Error');
      new Vue('vErreur500', ['e' => $e], 'gabarit-erreur');
    }  
  }
}