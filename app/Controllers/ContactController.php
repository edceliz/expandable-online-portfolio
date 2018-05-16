<?php
  namespace Controllers;

  class ContactController extends Controller {
    function Index($request) {
      $status = isset($request['url'][0]) ? $request['url'][0] : false;
      $website = \Models\Configuration::all();
      self::render('contact', [
          'user' => \Models\Configuration::all(),
          'token' => \Authentication::generateCSRFToken('contact'),
          'status' => $status,
          'name' => $website->name,
          'description' => $website->tagline,
          'profile' => $website->profile,
          'recaptcha' => $_ENV['RECAPTCHA_CLIENT']
        ]
      );
    }

    function Inquire($request) {
      $form = $request['form'];
      if (isset($form['token'])
        && \Authentication::verifyCSRFToken('contact', $form['token'])
        && isset($form['g-recaptcha-response'])
        && \Authentication::validateCaptcha($form['g-recaptcha-response'])->success
        && !empty($form['name'])
        && !empty($form['email'])
        && !empty($form['message'])
      ) {
        $message = "{$form['name']} ({$form['email']}) inquired from your portfolio. {$form['message']}";
        if (mail(\Models\Configuration::all()->email, 'Website Inquiry', $message)) {
          header('location: /contact/index/success');
        } else {
          header('location: /contact/index/error');
        }
      } else {
        header('location: /contact/index/error');
      }
    }

    function Resume() {
      $file_url = \Models\Configuration::all()->resume;
      header('Content-Type: application/octet-stream');
      header('Content-Transfer-Encoding: Binary');
      header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
      readfile($file_url);
    }
  }