<?php
  class Bootstrap {
    static function run() {
      require_once('../vendor/autoload.php');
      $dotenv = new Dotenv\Dotenv('../');
      $dotenv->load();
      new Router();
    }
  }