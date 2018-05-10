<?php
  namespace Models;

  class User extends Model {
    protected $fillables = [
      'username',
      'password'
    ];
  }