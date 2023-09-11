<?php

class Vue {

  /**
   * génération de la page html en intégrant les données variables dans des templates Twig
   * @param string $vue, nom de la vue twig spécifique
   * @param array  $donnees, variables php à intégrer dans la page html
   * @param string $gabarit, nom du gabarit twig
   */
  public function __construct($vue, $donnees = [], $gabarit = 'gabarit-frontend') {
   
    require_once 'app/vues/vendor/autoload.php';

    $loader = new \Twig\Loader\FilesystemLoader('app/vues/templates');
    $twig = new \Twig\Environment(
      $loader,
      [
        // 'cache' => 'app/vues/templates/cache',
      ]
    );

    $donnees['templateMain'] = "$vue.twig";
    echo $twig->render("$gabarit.twig", $donnees);
  }
}