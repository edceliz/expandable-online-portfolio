<?php
  class Authentication {
    static function generateCSRFToken($name) {
      $_SESSION[$name . '_token'] = bin2hex(random_bytes(32));
      return $_SESSION[$name . '_token'];
    }

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

    static function login($username, $password) {
      $db = Database::getConnection();
      $stmt = $db->prepare('SELECT id, password from users WHERE username = ? LIMIT 1');
      $stmt->bind_param('s', $username);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();
      $stmt->close();
      $db->close();
      if ($result && password_verify($password, $result['password'])) {
        $_SESSION['uid'] = $result['id'];
        $_SESSION['username'] = $username;
        return true;
      } else {
        return false;
      }
    }

    static function verifyPassword($password) {
      $db = Database::getConnection();
      $stmt = $db->prepare('SELECT password from users WHERE id = ? LIMIT 1');
      $stmt->bind_param('i', self::getUser()->uid);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();
      $stmt->close();
      $db->close();
      return password_verify($password, $result['password']);
    }

    static function getUser() {
      $user = (object) ['id' => null, 'username' => null];
      if (isset($_SESSION['uid'])) {
        $user->id = $_SESSION['uid'];
        $user->username = $_SESSION['username'];
      }
      return $user;
    }
  }