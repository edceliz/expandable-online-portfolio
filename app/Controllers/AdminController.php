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
      self::render('admin-works', ['works' => \Models\Work::all()->result]);
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
        $profile = false;
        if ($request['files']['profile']['error'] !== 4) {
          $profile = \Upload::image($request['files']['profile'], 'img/', 'profile');
        }
        unset($request['form']['token']);
        $request['form']['profile'] = $profile;
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

    function Work($request) {
      $this->middleware();
      if (isset($request['url'][0]) && (int) $request['url'][0]) {
        self::render('admin-work',
          array_merge(
            ['token' => \Authentication::generateCSRFToken('admin_work')], 
            \Models\Work::find('id', $request['url'][0])->result[0])
          );
      } else {
        self::render('admin-work', [
          'token' => \Authentication::generateCSRFToken('admin_work'), 
          'id' => false
        ]);
      }
    }

    function AddWork($request) {
      $this->middleware();
      if (isset($request['form']['token'])
        && \Authentication::verifyCSRFToken('admin_work', $request['form']['token'])
      ) {
        $blankWork = [
          'title' => '',
          'subtitle' => NULL,
          'description' => '',
          'image' => NULL,
          'link_text' => '',
          'link_url' => ''
        ];
        $work = array_filter(array_merge($blankWork, $request['form']));
        unset($work['token']);
        if (isset($request['files']['image']) && $request['files']['image']['error'] !== 4) {
          $work['image'] = \Upload::image($request['files']['image']);
          if (!$work['image']) {
            header('location: /admin/work/error/1');
            die();
          }
        }
        \Models\Work::create([$work]);
        header('location: /admin');
      } else {
        header('location: /admin/work/error/1');
      }
    }

    function UpdateWork($request) {
      if (!\Authentication::verifyCSRFToken('admin_work', $request['form']['token']) || !(int) $request['url'][0]) {
        header('location: /admin');
        die();
      }
      switch ($request['form']['operation']) {
        case 'Create/Update Work':
          $work = $request['form'];
          unset($work['token'], $work['operation']);
          if (isset($request['files']['image']) && $request['files']['image']['error'] !== 4) {
            $work['image'] = \Upload::image($request['files']['image']);
            if (!$work['image']) {
              header('location: /admin/work/' . $request['url'][0]);
              die();
            }
          }
          \Models\Work::find('id', (int) $request['url'][0])->update($work);
          header('location: /admin');
          break;
        case 'Remove Image':
          \Models\Work::find('id', (int) $request['url'][0])->update(['image' => NULL]);
          header('location: /admin');
          break;
        case 'Delete':
          \Models\Work::find('id', (int) $request['url'][0])->delete();
          header('location: /admin');
          break;
        default:
          header('location: /admin/work/' . $request['url'][0]);
          break;
      }
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