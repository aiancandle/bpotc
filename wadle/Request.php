<?php
class Request
{
  public $response;
  public $request;
  public $auth;
  // public $redis;
  public function __construct($request,$auth,$response)
  {
    $this->auth=$auth;
    $this->response=$response;
    $this->request=$request;
  }
}
?>
