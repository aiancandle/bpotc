<?php
namespace APP\Http\Controllers;
use  \APP\Models\Orders;
use  \APP\Models\Configs;
class Admin extends \Request
{
  public function time($request){
    return response(SUCCESS,date("Y-m-d H:i:s"));
  }

  public function index($request){
    if(file_exists(APP_PATH.'/data/money.db')){
      return response(VIEW,'admin.login',[
        'COMPANY_ABBREVIATION'=>COMPANY_ABBREVIATION,
        'LOGIN_FAIL'=>'false',
      ]);
    }else{
      return response(VIEW,'admin.index',[
        'CONFIGS_URL'=>'/setConfigs',
        'ORDER_HIDE'=>'',
        'CONFIGS_SHOW'=>'layui-show',
        'SETTING_HIDE'=>"style=\"display:none;\"",
        'TITLE'=>'系统初始化配置',
        'SETTING_MODO'=>'true',
        'BUTTON_CONTENT'=>'初始化',
      ]);
    }
  }

  public function install($request)
  {
    if(file_exists(APP_PATH.'/data/money.db'))return response(REFUSE,'系统已经配置成功');
    if(copy(APP_PATH.'/data/money',APP_PATH.'/data/money.db')){
      $configs=new Configs();
      $request->id=1;
      $rc=$configs->insert($request);
      if($rc){
        \Tu::$wadle->shutdown();
        return response(SUCCESS,'配置成功，系统将在安装后重启');
      }else{
        unlink(APP_PATH.'/data/money.db');
        \Tu::$wadle->shutdown();
        return response(FAIL,'配置失败，系统将在五秒钟后自爆');
      }
    }else{
      return response(FAIL,'意外错误，请检查数据文件是否存在');
    }
  }

  public function hardRestart($request)
  {
    \Tu::$wadle->shutdown();
    return response(SUCCESS,'正在重启');
  }

  public function softRestart($request)
  {
    \Tu::$wadle->reload();
    return response(SUCCESS,'重启完毕');
  }

  public function reset($request)
  {
    if(file_exists(APP_PATH.'/data/money.db')){
      if(copy(APP_PATH.'/data/money.db',APP_PATH.'/data/copy/'.date('YmdHis').'.db')){
         unlink(APP_PATH.'/data/money.db');
      }else{
        return response(REFUSE,'系统异常,请稍后再试！');
      }
    }
    \Tu::$wadle->shutdown();
    return response(SUCCESS,'系统已重置，正在重启');
  }

  public function newConfigs($request)
  {
    $configs=new Configs(1);
    $rc=$configs->update($request);
    return response($rc==1?SUCCESS:FAIL);
  }

  public function getConfigs($request)
  {
    $configs=new Configs(1);
    return response(SUCCESS,null,$configs->array());
  }

  public function login($request)
  {
    if($request->LOGIN_ACCOUNT==LOGIN_ACCOUNT && $request->LOGIN_PASSWORD==LOGIN_PASSWORD && $request->DYNAMIC_PASSWORD==(date('Hi')+1111))
    {
      $session=session();
      \Tu::$table->set(1,['USER_SESSION'=>$session]);
      $this->response->cookie('USER_SESSION',$session,time()+86400,'/');
      return response(VIEW,'admin.index',[
        'CONFIGS_URL'=>'/newConfigs',
        'ORDER_HIDE'=>'layui-show',
        'CONFIGS_SHOW'=>'',
        'SETTING_HIDE'=>'',
        'TITLE'=>'入金管理系统',
        'SETTING_MODO'=>"false",
        'BUTTON_CONTENT'=>'立即提交',
      ]);
      //return response(SUCCESS,"登陆成功！");
    }
    return response(VIEW,'admin.login',[
      'COMPANY_ABBREVIATION'=>COMPANY_ABBREVIATION,
      'LOGIN_FAIL'=>'true',
    ]);
  }

  public function logout($request)
  {
    $this->response->cookie('USER_SESSION',"123",123,'/');
    return response(VIEW,'admin.login',[
      'COMPANY_ABBREVIATION'=>COMPANY_ABBREVIATION,
      'LOGIN_FAIL'=>'false',
    ]);
  }

  public function audit($request)
  {
    $order=new Orders($request->id);

    if($order->callback_result=="回调成功"){
      return response(REFUSE,"此订单已经回调成功，任何操作都将是徒劳的！");
    }

    if($order->state==$request->state){
      return response(REFUSE,"你已于 ".$order->audit_time." 已经执行过".$request->state."。");
    }

    $order->state=$request->state;
    $order->audit_time=dater();
    $rc=$order->execute();
    if($rc && AUTO_CALLBACK=="开启"){
      \Tu::$wadle->task(['do'=>'callback','id'=>$request->id,'try'=>1]);
    }
    return response($rc?SUCCESS:FAIL);
  }

  //回调操作
  public function callback($request)
  {
    $rc=\Tu::$wadle->taskwait(['do'=>'callback','id'=>$request->id,'try'=>"no"]);
    switch ($rc){
      case "回调失败":
        return response(FAIL,"回调失败");
        break;
      case "回调成功":
        return response(SUCCESS,"回调成功");
        break;
      default:
        return response(REFUSE,$rc);
        break;
    }
   }

   //入金币管理
   public function cashManagement($request)
   {
     $orders=new Orders();
     // $orders->output=['id','orderNo','orderAmount','create_time','state','name','card_owner','equal_dollar','confirm_time'];
     $orders->paginate($request->page,$request->limit);
     $data=$orders->array();
     $total=$orders->total;
     return response(SUCCESS,$total,$data);
   }

   public function orderDetails($request){
     $orders=new Orders($request->id);
     $sql=<<<EOF
     SELECT `id` as `ID`,`pickupUrl` as `跳转地址`,`receiveUrl` as `回调地址`,`signType` as `签名类型`,`orderNo` as `订单类型`,`orderAmount` as `订单金额`,`orderCurrency` as `币种`,`customerId` as `客户ID`,`create_time` as `订单创建时间`,`state` as `订单状态`,`audit_time` as `审核时间`,`callback_result` as `回调时间`,`name` as `回调时间`,`callback_content` as `回调内容`,`callback_time` as `回调时间`,`confirm_time` as `支付时间`,`callback_times` as `回调次数`,`card_owner` as `承兑商`,`equal_dollar` as `等值美元` FROM `orders` WHERE `id` =
EOF;
     return response(SUCCESS,null,$orders->fetch($sql.$request->id));
   }

   //回调日志
   public function callbackLog($request)
   {
     $orders=new Orders();
     $orders->output=['id','orderNo','callback_result','callback_time'];
     $orders->paginate($request->page);
     $data=$orders->array();
     $total=$orders->total;
     return response(SUCCESS,null,['total'=>$total,'content'=>$data]);
   }

   //下载表格
   public function excel($request)
   {
     $orders=new Orders();
     $data=$orders->array();
     $filename=date("YmdHis").".xls";
     $str = "<html><body>";
     $str .= "<table border=1>";
     $str .= "<tr>";
     foreach (array_keys($data[0]) as $k => $v)
     $str .= "<td>{$v}</td>";
     $str .= "</tr>";
     foreach ($data as $key => $rt) {
        $str .= "<tr>";
        foreach ($rt as $k => $v)
        $str .= "<td>{$v}</td>";
        $str .= "</tr>";
     }
     $str .= "</table></body></html>";
     $this->response->header('Content-Type',"application/vnd.ms-excel; name='excel'");
     $this->response->header('Content-Type',"application/octet-stream");
     $this->response->header('Content-Disposition',"attachment; filename=".$filename);
     $this->response->header('Cache-Control',"must-revalidate, post-check=0, pre-check=0");
     $this->response->header('Pragma',"no-cache");
     $this->response->header('Expires',"0");
     return response(PLAIN,$str);
   }
}
