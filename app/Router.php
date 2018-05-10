<?php
  class Router {
    private $controller;
    private $action = 'Index';
    private $params = [
      'url' => [], 
      'form' => [],
      'files' => []
    ];

    function __construct() {
      $request = $_REQUEST;
      $url = $this->parseURL($request['url']);
      $this->controller = ucfirst($url[0]) ?: 'Index';
      $this->controller = $this->controller === 'Index.php' ? 'Index' : $this->controller;
      $this->controller .= 'Controller';
      array_shift($url);
      if (!$url) {
        $this->start();
      } else {
        $this->action = ucfirst($url[0]);
        array_shift($url);
        $this->params['url'] = $url;
        unset($request['url']);
        $this->params['form'] = $request;
        $this->params['files'] = $_FILES;
        $this->start();
      }
    }

    function parseURL($params) {
      return explode('/', filter_var(rtrim($params, '/'), FILTER_SANITIZE_URL));
    }

    private function start() {
      $controller = $this->controller;
      if (!file_exists(__DIR__ . "/Controllers/{$controller}.php")
        || !method_exists("Controllers\\{$controller}", $this->action)
      ) {
        $controller = 'ErrorController';
        $this->action = 'Index';
      }
      $controller = 'Controllers\\' . $controller;
      call_user_func_array([new $controller, $this->action], [$this->params]);
    }
  }