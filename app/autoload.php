<?php
  /**
   * This loader expects the class to be the same with file name.
   */
  spl_autoload_register(function($class) {
      $class = str_replace('\\', '/', $class);
      $class = __DIR__ . '/' . $class . '.php';
      include_once($class);
  });