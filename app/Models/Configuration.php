<?php
  namespace Models;

  class Configuration {
    private static $file = '../configuration.json';

    static function getAll() {
      return json_decode(file_get_contents(self::$file));
    }

    static function update($update) {
      $configuration = self::getAll();
      foreach ($update as $key => $value) {
        if ($value) {
          $configuration->$key = $value;
        }
      }
      file_put_contents(self::$file, json_encode($configuration));
    }
  }