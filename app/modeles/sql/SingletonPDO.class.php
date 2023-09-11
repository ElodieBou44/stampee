<?php

class SingletonPDO extends PDO {
  
  private static $instance = null;

    const DB_SERVER = 'localhost:8889';
    const DB_NOM = 'stampee';
    const DB_DSN = 'mysql:host='. self::DB_SERVER .';dbname='. self::DB_NOM.';charset=utf8';
    const DB_LOGIN = 'root';
    const DB_PASSWORD = 'root'; 

  /**
   * Constructeur pour initialiser un objet SingletonPDO qui hérite de la classe PDO
   */
  private function __construct() {
    $options = [
      PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,  // Gestion des erreurs par des exceptions de la classe PDOException
      PDO::ATTR_EMULATE_PREPARES  => true                     // Préparation des requêtes émulée
    ];
    parent::__construct(self::DB_DSN, self::DB_LOGIN, self::DB_PASSWORD, $options); // Instanciation de la connexion PDO
  }
  
  private function __clone() {} // Empêche le clonage de cet objet 

  /**
   * Initialise la propriété $instance avec un objet SingletonPDO
   * @return SingletonPDO instance unique de cette classe
   */
  public static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new SingletonPDO();
    }
    return self::$instance;
  }
}