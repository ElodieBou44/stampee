<?php

/**
 * Classe des requêtes PDO 
 *
 */
class RequetesPDO {

  protected $sql;
  protected $params = []; 

  const UNE_SEULE_LIGNE = true;

  /**
   * Récupération d'une ou plusieurs lignes de la requête SELECT dans la propriété $sql 
   * en intégrant s'il y en a, les valeurs associées aux marqueurs de la requête préparée, dans le tableau de la propriété $params
   * @param bool $uneSeuleLigne 
   * @return array|false booléen false si aucun résultat avec $uneSeuleLigne à true
   */
public function getLignes($uneSeuleLigne = false) {
    $sPDO = SingletonPDO::getInstance();
    $oPDOStatement = $sPDO->prepare($this->sql);
    foreach ($this->params as $marqueur => $valeur) {
      $oPDOStatement->bindValue(":$marqueur", $valeur);
    } 
    $oPDOStatement->execute();
    return $uneSeuleLigne ? $oPDOStatement->fetch(PDO::FETCH_ASSOC) : $oPDOStatement->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Requête $sql de Création Update ou Delete d'une ligne
   * @return boolean|string chaîne contenant lastInsertId s'il est > 0
   */ 
  public function CUDLigne() {
    $sPDO = SingletonPDO::getInstance();
    $oPDOStatement = $sPDO->prepare($this->sql);
    foreach ($this->params as $nomParam => $valParam)   $oPDOStatement->bindValue(':'.$nomParam, $valParam);
    $oPDOStatement->execute();
    if ($oPDOStatement->rowCount() <= 0) return false;
    if ($sPDO->lastInsertId() > 0)       return $sPDO->lastInsertId();
    return true;
  }

}