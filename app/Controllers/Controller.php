<?php
  namespace Controllers;

  /**
   * Inheritable class that provides children class with rendering capability.
   */
  class Controller {
    /**
     * Outputs the selected view of the user with its parameters.
     *
     * @param string $view
     * @param array $params
     * @return void
     */
    static function render($view, $params = []) {
      $loader = new \Twig_Loader_Filesystem('../views');
      $settings = [
        'cache' => $_ENV['DEBUG'] === 'true' ? false : '../cache'
      ];
      $twig = new \Twig_Environment($loader, $settings);
      $lexer = new \Twig_Lexer($twig, [
        'tag_block' => ['{', '}'],
        'tag_variable' => ['{{', '}}']
      ]);
      $twig->setLexer($lexer);
      echo $twig->render($view . '.html', $params);
    }
  }