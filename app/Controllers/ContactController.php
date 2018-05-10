<?php
  namespace Controllers;

  class ContactController extends Controller {
    function Index() {
      self::render('contact');
    }
  }