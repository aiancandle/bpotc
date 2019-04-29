<?php
namespace APP\Http\Controllers;
use  \APP\Models\Accounts;
class Account extends \Request
{
  public function index($request)
  {
    $accounts = new Accounts;
    return response(SUCCESS,null,$accounts->array());
  }

  public function insert($request)
  {
    $accounts = new Accounts;
    $rc=$accounts->insert($request);
    return response($rc?SUCCESS:FAIL);
  }

  public function update($request)
  {
    $accounts = new Accounts($request->id);
    $rc=$accounts->update($request);
    return response($rc?SUCCESS:FAIL);
  }

  public function delete($request)
  {
    $accounts = new Accounts($request->id);
    $rc=$accounts->delete();
    return response($rc?SUCCESS:FAIL);
  }
}
