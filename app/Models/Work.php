<?php
  namespace Models;

  class Work extends Model {
    protected $fillables = [
      'title',
      'subtitle',
      'description',
      'image',
      'link_text',
      'link_url'
    ];
  }