<?php
  class Upload {
    static function validateFile($file, $allowedMimes, $maxSize = 5242880) {
      if ($file['error']) {
        return false;
      }
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $file['tmp_name']);
      if (!in_array($mime, $allowedMimes) || $file['size'] > $maxSize) {
        return false;
      }
      return true;
    }

    static function complete($file, $destination, $name) {
      $location = $destination . $name . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
      move_uploaded_file($file['tmp_name'], $location);
      return $location;
    }

    static function image($image, $destination = 'img/portfolio/', $maxSize = 5242880, $name = false) {
      if (!self::validateFile($image, [
        'image/jpeg', 
        'image/png', 
        'image/gif', 
        'image/x-ms-bmp'
      ], $maxSize)) {
        return false;
      }
      return self::complete($image, $destination, $name ?: bin2hex(random_bytes(5)));
    }
  }