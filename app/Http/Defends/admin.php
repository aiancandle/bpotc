<?php
namespace APP\Http\Defends;
use \Defend;
Abstract class Admin
{
  static $configs=['LOGIN_ACCOUNT','LOGIN_PASSWORD','CONFIRM_PASSWORD','EMAIL_SENDER','LEANWOREK_KEY','SERVER_OPEN','MAX_VALUE','MIN_VALUE','EMAIL_PASSWORD','EMAIL_SUBJECT','EMAIL_RECEIVER','AUTO_CALLBACK','EXCHANGE_RATE','COMPANY_ABBREVIATION','COMPANY_NAME'];
  static function configs($data)
  {
    if(isset($data->request['CONFIRM_PASSWORD']) && isset($data->request['LOGIN_PASSWORD']) && $data->request['CONFIRM_PASSWORD']==$data->request['LOGIN_PASSWORD']){
      return response(REFUSE,'确认密码不可以和登录密码相同！');
    }
    return validate(at_least_exist(accept($data,self::$configs),self::$configs),[
      'LOGIN_ACCOUNT'=>'lenth:6,20',
      'LOGIN_PASSWORD'=>'lenth:6,20',
      'CONFIRM_PASSWORD'=>'lenth:6,20',
      'EMAIL_SENDER'=>'email',
      'LEANWOREK_KEY'=>'lenth:6,20',
      'SERVER_OPEN'=>'enum:开启,关闭',
      'AUTO_CALLBACK'=>'enum:开启,关闭',
      'MAX_VALUE'=>'digits',
      'MIN_VALUE'=>'digits',
      'EXCHANGE_RATE'=>'digits',
      'EMAIL_PASSWORD'=>'lenth:6,20',
      'EMAIL_SUBJECT'=>'lenth:6,20',
      'EMAIL_RECEIVER'=>'email',
      'COMPANY_ABBREVIATION'=>'lenth:6,80',
      'COMPANY_NAME'=>'lenth:6,80',
    ]);
  }

  static function install($data)
  {
    return validate(accept($data,self::$configs),[
      'LOGIN_ACCOUNT'=>'required',
      'LOGIN_PASSWORD'=>'required',
      'CONFIRM_PASSWORD'=>'required',
      'EMAIL_SENDER'=>'required',
      'EMAIL_PASSWORD'=>'required',
      'EMAIL_SUBJECT'=>'required',
      'EMAIL_RECEIVER'=>'required',
      'LEANWOREK_KEY'=>'required',
      'SERVER_OPEN'=>'required',
      'AUTO_CALLBACK'=>'required',
      'MAX_VALUE'=>'required',
      'MIN_VALUE'=>'required',
      'EXCHANGE_RATE'=>'required',
      'COMPANY_ABBREVIATION'=>'required',
      'COMPANY_NAME'=>'required',
    ]);
  }

  static function callback($data)
  {
    return validate($data,[
      'id'=>'id|required',
    ]);
  }

  static function audit($data)
  {
    return validate($data,[
      'id'=>'id|required',
      'state'=>'required|enum:确认收款,订单关闭',
    ]);
  }

  static function page($data)
  {
    return validate($data,[
      'page'=>'page|required',
    ]);
  }

  static function login($data)
  {
    return validate($data,[
      'LOGIN_ACCOUNT'=>'required',
      'LOGIN_PASSWORD'=>'required',
      'DYNAMIC_PASSWORD'=>'required',
    ]);
  }

  static function logined($data)
  {
    if(isset($data->auth['USER_SESSION']))
    return $data;
    return response(REFUSE,"PLEASE LOGIN IN");
  }

  static function confirm($data)
  {
    if(isset($data->request['NOW_CONFIRM_PASSWORD']) && $data->request['NOW_CONFIRM_PASSWORD']===CONFIRM_PASSWORD){
      unset($data->request['NOW_CONFIRM_PASSWORD']);
      return $data;
    }
    return response(REFUSE,'确认密码错误！');
  }

  static function details($data)
  {
    return validate($data,[
      'page'=>'id|required',
    ]);
  }
}

Defend::group(Admin::class,function(){
  Defend::register('admin.configs','configs');
  Defend::register('admin.callback','callback');
  Defend::register('admin.audit','audit');
  Defend::register('admin.logined','logined');
  Defend::register('admin.login','login');
  Defend::register('admin.confirm','confirm');
  Defend::register('admin.page','page');
  Defend::register('admin.install','install');
});
