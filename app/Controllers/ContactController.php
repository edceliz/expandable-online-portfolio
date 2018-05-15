<?php
  namespace Controllers;

  class ContactController extends Controller {
    function Index() {
      self::render('contact', ['user' => \Models\Configuration::all()]);
    }

    function Resume() {
      $file_url = \Models\Configuration::all()->resume;
      header('Content-Type: application/octet-stream');
      header('Content-Transfer-Encoding: Binary');
      header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
      readfile($file_url);
    }
  }