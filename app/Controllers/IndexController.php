<?php
  namespace Controllers;

  class IndexController extends Controller {
    function Index() {
      $website = \Models\Configuration::all();
      self::render('index', [
        'works' => \Models\Work::all()->result, 
        'name' => $website->name,
        'description' => $website->tagline,
        'profile' => $website->profile
        ]
      );
    }
  }