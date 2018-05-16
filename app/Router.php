<?php
  /**
   * A dynamic router that parses URL into controller, action and parameters.
   */
  class Router {
    /**
     * The specified controller base on the URL.
     *
     * @var string
     */
    private $controller;

    /**
     * The function of the controller to be performed.
     *
     * @var string
     */
    private $action = 'Index';

    /**
     * URL - Parameters included on the requested URL.
     * FORM - Contains values submitted using a form (POST)
     * FILES - Contains files that are submitted using form (POST)
     * 
     * @var array
     */
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

    /**
     * Call the specified action of the controller and pass the parameters.
     *
     * @return void
     */
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