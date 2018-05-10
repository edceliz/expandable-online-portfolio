<?php
  namespace Controllers;

  class ErrorController extends Controller {
    function Index() {
      self::render('error');
    }
  }