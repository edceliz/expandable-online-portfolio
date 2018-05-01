<?php
  session_start();
  require_once('../app/autoload.php');
  require_once('../vendor/autoload.php');
  $dotenv = new Dotenv\Dotenv('../');
  $dotenv->load();
  new Bootstrap();