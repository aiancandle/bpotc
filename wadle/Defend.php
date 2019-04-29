<?php
class Defend
{
  static $controller;
  static $newone=0;
  public static function group($controller,callable $group)
  {
    self::$newone=1;
    self::$controller=$controller;
    $group();
    self::$newone=0;
    return new self;
  }

  public static function register($nickname,$method,$controller=null)
  {
    if(self::$controller){
      $controller=self::$controller;
      $GLOBALS["DEFENDERS"][$nickname]=$controller.'@'.$method;
    }else if($controller){
      $GLOBALS["DEFENDERS"][$nickname]=$controller.'@'.$method;
      return new self;
    }else{
      throw new Exception($nickname." register failed!\n");
    }
  }

  public static function response($code,$message=null)
  {
    return (object)["code"=>$code,"message"=>$message];
  }

  public function __construct()
  {
    if(self::$newone==0)
    self::$controller=null;
  }
}
