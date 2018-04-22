<?php
  class Bootstrap {
    private $controller;
    private $action = 'index';
    private $params = [
      'url' => [], 
      'form'=> []
    ];

    function __construct() {
      $request = $_REQUEST;
      $url = $this->parseURL($request['url']);
      $this->controller = $url[0] ?: 'index';
      $this->controller = $this->controller == 'index.php' ? 'index' : $this->controller;
      array_shift($url);
      if (!$url) {
        $this->start();
      } else {
        $this->action = $url[0];
        array_shift($url);
        $this->params['url'] = $url;
        unset($request['url']);
        $this->params['form'] = $request;
        $this->start();
      }
    }

    function parseURL($params) {
      return explode('/', filter_var(rtrim($params, '/'), FILTER_SANITIZE_URL));
    }

    function start() {
      $controllerAction = ucfirst($this->controller).ucfirst($this->action);
      if (!method_exists('Controllers', $controllerAction)) {
        $controllerAction = 'Error404';
      }
      call_user_func_array([new Controllers, $controllerAction], $this->params);
    }
  }