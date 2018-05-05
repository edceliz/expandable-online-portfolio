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
      echo $this->twig->render('admin-works.html');
    }

    function AdminSettings($request) {
      if (!Authentication::checkLogin()) {
        header('location: /admin/login');
        die();
      }
      if (isset($request['form']['token'])
        && Authentication::verifyCSRFToken('admin_settings', $request['form']['token'])
      ) {
        $file = false;
        if ($request['files']['resume']['error'] !== 4) {
          if (!Upload::validateFile($request['files']['resume'], 
            [
              'application/msword', 
              'application/pdf',
              'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            10485760)
          ) {
            header('location: /admin/settings');
            die();
          }
          $file = Upload::complete($request['files']['resume'], 'downloadables/', 'resume');
        }
        array_shift($request['form']);
        $request['form']['resume'] = $file;
        Models\Configuration::update($request['form']);
        header('location: /admin/settings');
        die();
      }
      $configuration = Models\Configuration::getAll();
      echo $this->twig->render('admin-settings.html', [
        'current' => $configuration, 
        'token' => Authentication::generateCSRFToken('admin_settings'),
        'user' => Authentication::getUser()
        ]
      );
    }

    function AdminAccount($request) {
      if (!Authentication::checkLogin()) {
        header('location: /admin/login');
        die();
      }
      if (!isset($request['form']['token'])
        || !Authentication::verifyCSRFToken('admin_settings', $request['form']['token'])
        || !Authentication::verifyPassword($request['form']['old_password'])
      ) {
        // header('location: /admin/settings');
        // die();
      }
      if ($request['form']['username'] !== Authentication::getUser()->username || !empty($request['form']['password'])) {
        Models\User::all();
        die();
      }
      if ($request['form']['email'] !== Models\Configuration::getAll()->email) {
        Models\Configuration::update(['email' => $request['form']['email']]);
      }
      header('location: /admin/settings');
    }

    function AdminWork() {
      if (!Authentication::checkLogin()) {
        header('location: /admin/login');
        die();
      }
      echo $this->twig->render('admin-work.html');
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
        header('location: /admin');
      } else {
        echo $this->twig->render('login.html', ['token'=>Authentication::generateCSRFToken('admin_login')]);
      }
    }

    function AdminLogout() {
      Authentication::logout();
      header('location: /');
    }
  }