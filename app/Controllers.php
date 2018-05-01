<?php
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

    function ContactIndex() {
      echo $this->twig->render('contact.html');
    }

    function Error404() {
      echo $this->twig->render('error.html');
    }

    function AdminIndex() {
      if (!Authentication::checkLogin()) {
        header('location: /admin/login');
        die();
      }
    }
    
    function AdminLogin($request) {
      if (Authentication::checkLogin()) {
        header('location: /admin');
        die();
      }
      if (isset($request['form']['token']) 
        && Authentication::verifyCSRFToken('admin_login', $request['form']['token']) 
        && Authentication::login($request['form']['username'], $request['form']['password'])
      ) {
        var_dump($_SESSION);
      } else {
        echo $this->twig->render('login.html', ['token'=>Authentication::generateCSRFToken('admin_login')]);
      }
    }

    function AdminLogout() {
      Authentication::logout();
      header('location: /');
    }
  }