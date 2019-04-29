<?php
namespace APP\Http\Controllers;
class Home extends \Request
{
  public function index($request)
  {
    return response(VIEW,'client.test');
  }
}
