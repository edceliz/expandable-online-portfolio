<?php
  namespace Controllers;

  /**
   * Handles actions from index/home page
   */
  class IndexController extends Controller {
    /**
     * Default action of the application, renders home page.
     *
     * @return void
     */
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