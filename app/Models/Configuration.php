<?php
  namespace Models;

  class Configuration {
    private static $file = '../configuration.json';

    static function all() {
      return json_decode(file_get_contents(self::$file));
    }

    static function update($update) {
      $configuration = self::all();
      foreach ($update as $key => $value) {
        if ($key === 'resume' && !$value) {
          continue;
        }
        $configuration->$key = $value;
      }
      file_put_contents(self::$file, json_encode($configuration));
    }
  }