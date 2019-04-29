<?php
namespace APP\Http\Controllers;
use  \APP\Models\Orders;
class Trader extends \Request
{
  public function test($request)
  {
    return response(VIEW,'client.test',['orderNo'=>"TW".rand(100000000,9999999999999)]);
  }

  //记录姓名
  public function attribute($request){
    $body=array(
      'pickupUrl'    =>$request->pickupUrl,
      'receiveUrl'   =>$request->receiveUrl,
      'signType'     =>$request->signType,
      'orderNo'      =>$request->orderNo,
      'orderAmount'  =>$request->orderAmount,
      'orderCurrency'=>$request->orderCurrency,
      'customerId'   =>$request->customerId
    );
    $sign = strtolower(md5(implode('',$body).LEANWOREK_KEY));
    //if($sign!=$request->sign)return response(BAD_REQUEST);
    $body['create_time']=dater();
    $body['sign']=$sign;
    $body['equal_dollar']=sprintf("%.2f", $request->orderAmount/EXCHANGE_RATE);
    $body['state']='未知订单';
    $body['callback_result']='尚未回调';
    $body['callback_times']=0;
    $body['name']='----';
    $body['card_owner']='----';
    $orders=new Orders;
    if($orders->exist($request->orderNo,'orderNo')){
      return response(VIEW,'client.err',['msg'=>'重复的的订单请求']);
    }else{
      $rc=$orders->insert($body);
      if($rc){
        return response(VIEW,'client.yft',[
          'orderAmount'  =>$request->orderAmount,
          'equal_dollar'=>$body['equal_dollar'],
          "id"=>$rc,
          "COMPANY_NAME"=>COMPANY_NAME,
        ]);
      }else{
        return response(VIEW,'client.unkwon');
      }
    }
  }

  public function receive($request)
  {
    $order=new Orders($request->id);
    if($order->name!="----"){
      return response(VIEW,'client.err',['msg'=>'您已留下个人信息且已点击去付款按钮，请勿重复操作']);
    }else{
      if(USER_ACCOUNTS_NUM==1){
        $index=0;
      }else{
        $index=\Tu::$atomic->get();
        if($index>=USER_ACCOUNTS_NUM){
          $index=0;
          \Tu::$atomic->set(1);
        }else{
          \Tu::$atomic->add();
        }
      }
      $order->card_owner=USER_ACCOUNTS[$index]['CARD_OWNER'];
      $order->name=$request->realname;
      $order->state='等待支付';
      $rc=$order->execute();
      if($rc){
        return response(VIEW,'client.yft2',[
          'orderNo'      =>$order->orderNo,
          'orderAmount'  =>$order->orderAmount,
          'orderCurrency'=>$order->orderCurrency,
          'equal_dollar'=>$order->equal_dollar,
          'EMAIL_RECEIVER'=>EMAIL_RECEIVER,
          "BANK_NAME"=>USER_ACCOUNTS[$index]['BANK_NAME'],
          "CARD_NUMBER"=>USER_ACCOUNTS[$index]['CARD_NUMBER'],
          "CARD_OWNER"=>USER_ACCOUNTS[$index]['CARD_OWNER'],
          "COMPANY_ABBREVIATION"=>COMPANY_ABBREVIATION,
          "id"=>$request->id,
        ]);
      }else{
        return response(VIEW,'client.err',['msg'=>'异常错误！']);
      }
    }
  }

  public function confirm($request){
    $confirm_time=dater();
    $order=new Orders($request->id);
    if($order->state=="等待审核"){
      return response(VIEW,'client.err',['msg'=>'您已确认支付，无需重复操作，请耐心等待！']);
    }
    $body=<<<EOF
    {$order->name} 在 {$confirm_time} 完成了订单 {$order->orderNo} ，金额为 {$order->orderAmount} 元，承兑商为 {$order->card_owner} ，请及时确认。
EOF;
    \Tu::$wadle->Task(['do'=>'email','body'=>$body]);
    $order->confirm_time=$confirm_time;
    $order->state='等待审核';
    $rc=$order->execute();

    if($rc){
      return response(VIEW,'client.bpotc',['pickupUrl'=>$order->pickupUrl]);
    }else{
      return response(VIEW,'client.err',['msg'=>'异常错误！']);
    }
  }
}
