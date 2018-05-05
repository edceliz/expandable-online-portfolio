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
  }