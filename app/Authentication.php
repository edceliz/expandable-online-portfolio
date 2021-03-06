<?php
  /**
   * Handles authentication related functions.
   */
  class Authentication {
    /**
     * Creates a token that can be used in forms for CSRF.
     *
     * @param string $name - Name of the token
     * @return string
     */
    static function generateCSRFToken($name) {
      $_SESSION[$name . '_token'] = bin2hex(random_bytes(32));
      return $_SESSION[$name . '_token'];
    }

    /**
     * Verifies the submitted token with the selected token name.
     *
     * @param string $name
     * @param string $token
     * @return void
     */
    static function verifyCSRFToken($name, $token) {
      $result = false;
      if (isset($_SESSION[$name . '_token'])) {
        $result = hash_equals($_SESSION[$name . '_token'], $token);
        unset($_SESSION[$name . '_token']);
      }
      return $result;
    }

    static function logout() {
      session_destroy();
    }

    static function checkLogin() {
      if (isset($_SESSION['uid'])) {
        return $_SESSION['uid'];
      } else {
        return false;
      }
      return $_SESSION['uid'] ?: false;
    }

    /**
     * Logs in user upon submission of correct credentials.
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    static function login($username, $password) {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare('SELECT id, password FROM users WHERE username = ? LIMIT 1');
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();
      $stmt->free_result();
      $stmt->close();
      if ($result && password_verify($password, $result['password'])) {
        $_SESSION['uid'] = $result['id'];
        $_SESSION['username'] = $username;
        return true;
      } else {
        return false;
      }
    }

    /**
     * Checks if submitted password is same with the logged in user's current password.
     *
     * @param string $password
     * @return void
     */
    static function verifyPassword($password) {
      $db = Database::getInstance()->getConnection();
      $stmt = $db->prepare('SELECT password from users WHERE id = ? LIMIT 1');
      $stmt->bind_param('i', self::getUser()->id);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();
      $stmt->close();
      return password_verify($password, $result['password']);
    }

    /**
     * Returns user's information.
     *
     * @return stdObject
     */
    static function getUser() {
      $user = (object) ['id' => null, 'username' => null];
      if (isset($_SESSION['uid'])) {
        $user->id = $_SESSION['uid'];
        $user->username = $_SESSION['username'];
      }
      return $user;
    }

    /**
     * Connects with reCAPTCHA API for validation.
     *
     * @param string $response
     * @return stdObject
     */
    static function validateCaptcha($response) {
      $parameters = [
        'secret' => $_ENV['RECAPTCHA_SERVER'],
        'response' => $response
      ];
      $curl_handle = curl_init();
      curl_setopt($curl_handle, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
      curl_setopt($curl_handle, CURLOPT_POST, 1);
      curl_setopt($curl_handle, CURLOPT_POSTFIELDS, http_build_query($parameters));
      curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
      $res = json_decode(curl_exec($curl_handle));
      curl_close($curl_handle);
      return $res;
    }
  }