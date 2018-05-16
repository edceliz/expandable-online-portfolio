<?php
  /**
   * Handles file handling.
   */
  class Upload {
    /**
     * Checks if file is valid according to its mime or size.
     *
     * @param array $file - Example is $parameters['files']['resume'] wherein $parameters is the sent data by the controller
     * @param array $allowedMimes - Array of allowed mimes.
     * @param integer $maxSize - Bytes
     * @return boolean
     */
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

    /**
     * Uploads file to specific destination and its new name.
     *
     * @param array $file - Example is $parameters['files']['resume'] wherein $parameters is the sent data by the controller
     * @param string $destination - Note that the root is '/public' folder.
     * @param string $name - Pure name without extension.
     * @return string - Path to the uploaded file.
     */
    static function complete($file, $destination, $name) {
      $location = $destination . $name . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
      move_uploaded_file($file['tmp_name'], $location);
      return $location;
    }

    /**
     * Uploads a valid image.
     *
     * @param [type] $image - Example is $parameters['files']['profile'] wherein $parameters is the sent data by the controller
     * @param string $destination - Note that the root is '/public' folder.
     * @param boolean $name - Pure name without extension.
     * @param integer $maxSize - Byte
     * @return void
     */
    static function image($image, $destination = 'img/portfolio/', $name = false, $maxSize = 5242880) {
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