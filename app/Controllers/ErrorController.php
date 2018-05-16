<?php
  namespace Controllers;

  /**
   * Handles error related view output.
   */
  class ErrorController extends Controller {
    /**
     * Renders a 404 error.
     *
     * @return void
     */
    function Index() {
      self::render('error');
    }
  }