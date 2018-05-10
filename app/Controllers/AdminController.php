<?php
  namespace Controllers;

  class AdminController extends Controller {
    private function middleware() {
      if (!\Authentication::checkLogin()) {
        header('location: /admin/login');
        die();
      }
    }

    function Index() {
      $this->middleware();
      self::render('admin-works');
    }

    function Settings() {
      $this->middleware();
      $configuration = \Models\Configuration::all();
      self::render('admin-settings', [
        'current' => $configuration, 
        'token' => \Authentication::generateCSRFToken('admin_settings'),
        'user' => \Authentication::getUser()
        ]
      );
    }

    function Website($request) {
      $this->middleware();
      if (isset($request['form']['token'])
        && \Authentication::verifyCSRFToken('admin_settings', $request['form']['token'])
      ) {
        $file = false;
        if ($request['files']['resume']['error'] !== 4) {
          if (!\Upload::validateFile($request['files']['resume'], 
            [
              'application/msword', 
              'application/pdf',
              'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ],
            10485760)
          ) {
            header('location: /admin/settings/error/1');
            die();
          }
          $file = \Upload::complete($request['files']['resume'], 'downloadables/', 'resume');
        }
        array_shift($request['form']);
        $request['form']['resume'] = $file;
        \Models\Configuration::update($request['form']);
        header('location: /admin/settings/success');
      } else {
        header('location: /admin/settings/error/1');
      }
    }

    function Account($request) {
      $this->middleware();
      if (!isset($request['form']['token'])
        || !\Authentication::verifyCSRFToken('admin_settings', $request['form']['token'])
        || !\Authentication::verifyPassword($request['form']['old_password'])
      ) {
        header('location: /admin/settings/error/1');
        die();
      }
      if ($request['form']['username'] !== \Authentication::getUser()->username || !empty($request['form']['password'])) {
        $update = ['username' => $request['form']['username']];
        $_SESSION['username'] = $update['username'];
        if (!empty($request['form']['password'])) {
          $update['password'] = password_hash($request['form']['password'], PASSWORD_DEFAULT);
        }
        \Models\User::find('id', \Authentication::getUser()->id)->update($update);
      }
      if ($request['form']['email'] !== \Models\Configuration::all()->email) {
        \Models\Configuration::update(['email' => $request['form']['email']]);
      }
      header('location: /admin/settings/success');
    }

    function Work() {
      $this->middleware();
      self::render('admin-work');
    }

    function Login($request) {
      if (isset($request['form']['token']) 
        && \Authentication::verifyCSRFToken('admin_login', $request['form']['token']) 
        && \Authentication::login($request['form']['username'], $request['form']['password'])
      ) {
        header('location: /admin');
      } else {
        self::render('login', ['token' => \Authentication::generateCSRFToken('admin_login')]);
      }
    }

    function Logout() {
      \Authentication::logout();
      header('location: /');
    }
  }