<?php
  require_once '../vendor/autoload.php';

  class Controllers {
    function __construct() {
      $loader = new Twig_Loader_Filesystem('../views');
      $settings = [
        // 'cache' => '../cache'
      ];
      $this->twig = new Twig_Environment($loader, $settings);
      $lexer = new Twig_Lexer($this->twig, [
        'tag_block' => ['{', '}'],
        'tag_variable' => ['{{', '}}']
      ]);
      $this->twig->setLexer($lexer);
    }

    function IndexIndex() {
      echo $this->twig->render('index.html');
    }

    function Error404() {
      echo $this->twig->render('error.html', array('name' => 'error'));
    }
  }