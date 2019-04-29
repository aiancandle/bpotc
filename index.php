<?php
define('APP_PATH',dirname(__FILE__));
require 'wadle/Init.php';

class Wadle
{
  private $server;

  public function __construct()
  {
    //$this->wadle = new swoole_websocket_server(SWOOLE_LISTEN,SWOOLE_PORT);
    $this->wadle =new Swoole\Http\Server(SWOOLE_LISTEN,SWOOLE_PORT);
    $this->wadle->atomic=new swoole_atomic(0);
    $this->wadle->table=$this->table();
    // $this->wadle->on('open',       [$this,'onOpen']);
    // $this->wadle->on('message',    [$this,'onMessage']);
    // $this->wadle->on('close',      [$this,'onClose']);
    $this->wadle->on('finish',     [$this,'onFinish']);
    $this->wadle->on('WorkerStart',[$this,'onWorkerStart']);
    $this->wadle->on('task',       [$this,'onTask']);
    $this->wadle->on('request',    [$this,'onRequest']);
    $this->wadle->on('start',    [$this,'onStart']);
    $this->wadle->set(
        [
            //'daemonize' => 1,//守护进程
            'worker_num' =>10,
            'task_worker_num'=>50,
            'enable_static_handler' => true,
            'document_root' => APP_PATH.'/front/public',//注意源码泄露
            'package_max_length' => 10485760,
        ]);
    $this->wadle->start();

  }

  public function onStart()
  {
    swoole_set_process_name('live_master');//设置主进程别名
  }

  //创建table表
  public function table()
  {
    $table = new swoole_table(1);
    $table->column('USER_SESSION', swoole_table::TYPE_STRING,32);
    $table->create();

    return $table;
  }

  // public function onOpen($ws,$request)
  // {
  //
  // }
  //
  // public function onMessage($ws,$frame)
  // {
  //
  // }

  // public function onClose($ws,$fd)
  // {
  //
  // }

  public function onTask($server, $task_id, $from_id, $data)
  {
    switch ($data['do']) {
      case 'callback':
        $order=new \APP\Models\Orders($data["id"]);
        if($order->state!="确认收款"){
          if($data['try']=="no"){
            $server->finish("此订单尚未确认收款，无法进行回调！");
          }
          return;
        }
        if($order->callback_result=="回调成功"){
          if($data['try']=="no"){
            $server->finish("已经回调成功，请勿重复操作！");
          }
          return;
        }
        $body=array(
          'signType'     =>"MD5",
          'orderNo'      =>$order->orderNo,
          'orderAmount'  =>$order->orderAmount,
          'orderCurrency'=>"CNY",
          'transactionId'=>$order->id,
          'status'       =>'success'
        );
        $sign = strtolower(md5(implode("",$body).LEANWOREK_KEY));
        $body["sign"]=$sign;
        HHHH:
        $time=dater();

        // $http=new \Swoole\Coroutine\Http\Client("publicapi.lwork.com",8080);
        // $http->post('/notify/default_notify',$body);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://publicapi.lwork.com:8080/notify/default_notify");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        $response = curl_exec($ch);
        curl_close($ch);

        $veri=strpos($response,'success');
        //$veri=strpos($http->body,'success');
        $result=$veri===false?"回调失败":"回调成功";
        // $http->close();

        //$order->callback_result=$result;
        //$order->callback_time=$time;
        //$order->callback_times=$order->callback_times+1;
        // $order->callback_content=$http->body;
        //$order->callback_content=$response;
        //$rc=$order->execute();

        $rc=$order->exec("UPDATE `orders` SET `callback_result` = '{$result}',`callback_time` = '{$time}',`callback_times` = `callback_times`+1,`callback_content` = '{$response}' WHERE `callback_result`!='回调成功' AND id = ".$order->id);

        if($data['try']==="no"){
          $server->finish($result);
        }elseif($result=="回调失败" && $data['try']<5){
          $data['try']+=1;
          swoole_timer_after($data['try']*100000,function(){

          });
          sleep($data['try']*10);
          goto HHHH;
        }
        break;

      case 'email':
      $i=0;
      $mail=new \QQMailer;
      $mail->receiver=EMAIL_RECEIVER;
      $mail->subject=EMAIL_SUBJECT;
      $mail->body=$data['body'];
      $mail->sender=EMAIL_SENDER;
      $mail->password=EMAIL_PASSWORD;
      $mail->attach=isset($data['attach'])?$data['attach']:null;//APP_PATH."/index.php";
      HELL:
      $rc=$mail->send();
      if($rc!=1 && $i<5){
        $i++;
        goto HELL;
      }
      unset($mail);
        break;
    }
  }

  public function onFinish($server, $task_id, $data)
  {

  }

  public function onWorkerStart($server,$worker_id)
  {
    //加载业务层
    LOAD_FILE('worker',APP_PATH);

    Tu::$wadle=$this->wadle;
    Tu::$table=$this->wadle->table;
    Tu::$atomic=$this->wadle->atomic;
  }

  public function onRequest($request,$response)
  {
    // $views=include APP_PATH.'/front/view/routers.php';
    // if(isset($views[$request->server['request_uri']]))goto FRONT;
    // unset($views);

    //defer(function(){shell_exec('kill -USR1 '.shell_exec('pidof live_master'));});
    //defer(function(){\Tu::$wadle->reload(false);});

    $response->header('Access-Control-Allow-Credentials','true');
    $response->header('Access-Control-Allow-Headers','*');
    $response->header('Access-Control-Max-Age','3600');
    $response->header('Access-Control-Request-Method','GET,POST,PUT,DELETE,OPTIONS');
    $response->header('Access-Control-Allow-Origin','*');
    if($request->server['request_method']=='OPTIONS')goto OPTIONS;

    // $views=include APP_PATH.'/front/view/routers.php';
    if(isset($views[$request->server['request_uri']]))goto FRONT;
    unset($views);
    $auth=null;
    // if(isset($request->cookie['id']) && isset($request->cookie['session']) && \TU::$table->exist($request->cookie['id']))
    // {
    //   $auth=\Tu::$table->get($request->cookie['id']);
    //   if($auth['session']!=$request->cookie['session']){
    //     $auth=null;
    //   }
    // }

    if(isset($request->cookie['USER_SESSION']))
    {
      $auth=\Tu::$table->get(1);
      if($auth['USER_SESSION']!=$request->cookie['USER_SESSION'])
      {
        $response->cookie('USER_SESSION','123',123,'/');
        $auth=null;
      }
    }

    $method=$request->server['request_method']=='POST'?((isset($request->post['method']) && isset(HTTP_METHOD[$request->post['method']]))?HTTP_METHOD[$request->post['method']]:'POST' ):$request->server['request_method'];
    $http=$GLOBALS[$method][$request->server['request_uri']]??null;
    if($http){
      $params=$method=='GET'?$request->get:(isset($request->get)?(isset($request->files)?array_merge($request->get,$request->post,$request->files):array_merge($request->get,$request->post)):isset($request->files)?array_merge($request->files,$request->post):$request->post);
      $data=(object)['request'=>$params,'auth'=>$auth,'code'=>SUCCESS];
      if(isset($http['Defender']))
      {
        $data=array_reduce($http['Defender'],function($data,$item){
        return $data->code==SUCCESS?$item['class']::{$item['method']}($data):$data;
        },$data);
        switch ($data->code){
          case SUCCESS:break;
          case VIEW:goto VIEW;
          case REFUSE:goto REFUSE;
          case REDIRECT:goto REDIRECT;
          case BAD_REQUEST:goto BAD_REQUEST;
          default:goto NOT_FIND;
        }
      }
      if(isset($http['Controller']))
      goto CONTROLLER;
      elseif(isset($http['View']))
      goto VIEW;
    }

    NOT_FIND:
    $response->status(NOT_FIND);
    $response->end();
    return;

    BAD_REQUEST:
    $response->status(BAD_REQUEST);
    $response->end();
    return;

    CONTROLLER:
    $data=(new $http['Controller']['class']($request,$auth,$response))->{$http['Controller']['method']}((object)$data->request);
    if(!isset($data->code))goto BAD_REQUEST;
    switch ($data->code){
      case VIEW:goto VIEW;
      case REDIRECT:goto REDIRECT;
      case PLAIN:goto PLAIN;
      case BAD_REQUEST:goto BAD_REQUEST;
      default:$response->end(json_encode($data));
    }
    return;

    REFUSE:
    $response->end(code(REFUSE,$data->message));
    return;

    VIEW:
    $response->end(view($data->message,$data->data??null));
    return;

    REDIRECT:
    $response->redirect($data->message,REDIRECT);
    return;

    PLAIN:
    $response->end($data->message);
    return;

    FRONT:
    $response->end(view($views[$request->server['request_uri']]));
    return;

    OPTIONS:
    $response->status(SUCCESS);
    $response->end();
  }
}
(new Wadle);
