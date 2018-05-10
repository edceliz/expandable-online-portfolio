<?php
  namespace Controllers;

  class Controller {
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