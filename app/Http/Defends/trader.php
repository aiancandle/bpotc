<?php
namespace APP\Http\Defends;
use \Defend;
Abstract class Trader
{
  static function attribute($data)
  {
    if(SERVER_OPEN!="开启")return response(VIEW,'client.err',['msg'=>'很抱歉，已暂停服务!']);

    if($data->request['orderAmount']<MIN_VALUE)return response(VIEW,'client.err',['msg'=>'交易金额不得低于 '.MIN_VALUE.' 元！']);
    if($data->request['orderAmount']>MAX_VALUE)return response(VIEW,'client.err',['msg'=>'交易金额不得高于 '.MAX_VALUE.' 元！']);
    if(USER_ACCOUNTS_NUM==0)return response(VIEW,'client.err',['msg'=>'没有匹配的承兑商']);
    return validate($data,[
      'pickupUrl'=>'required',
      'receiveUrl'=>'required',
      'signType'=>'required',
      'orderNo'=>'required',
      'orderAmount'=>'required',
      'orderCurrency'=>'required',
      'customerId'=>'required',
      'sign'=>'required',
    ]);
  }

  static function receive($data)
  {
    return validate($data,[
      'id'=>'id|required',
      'realname'=>'required',
    ]);
  }

  static function confirm($data)
  {
    return validate($data,[
      'id'=>'id|required',
    ]);
  }
}

Defend::group(Trader::class,function(){
  Defend::register('trader.attribute','attribute');
  Defend::register('trader.receive','receive');
  Defend::register('trader.confirm','confirm');
});
