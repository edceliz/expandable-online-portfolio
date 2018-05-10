<?php
  class Bootstrap {
    static function run() {
      require_once('../vendor/autoload.php');
      $dotenv = new Dotenv\Dotenv('../');
      $dotenv->load(true);
      if ($_ENV['DEBUG'] === 'false') {
        error_reporting(0);
        @ini_set('display_errors', 0);
      }
      new Router();
    }
  }