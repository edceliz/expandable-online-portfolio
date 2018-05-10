<?php
  namespace Controllers;

  class IndexController extends Controller {
    function Index() {
      self::render('index');
    }
  }