<?php
abstract class Validator
{
  //字符串长度
  public static function sd_lenth($v,$args)
  {
    if(!isset($args[1])){
      if(strlen($v)==11)
      return ['code'=>SUCCESS];
      return ['code'=>REFUSE,'message'=>' not '.$args[0].' words'];
    }
    if(strlen($v)>=$args[0] && strlen($v)<=$args[1])
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' not in '.$args[0].'~'.$args[1].' words'];
  }

  //限定值的可能性
  public static function sd_enum($v,$args)
  {
    if(in_array($v,$args))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' not in '.implode(',',$args)];
  }

  //最大最
  public static function sd_max($v,$args)
  {
    if((float)$v<=$args[0])
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' < '.$args[0]];
  }

  //最小值
  public static function sd_between($v,$args)
  {
    if((float)$v>=$args[0] && (float)$v<=$args[1])
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' > '.$args[0].' and < '.$args[1]];
  }

  //最小值
  public static function sd_min($v,$args)
  {
    if((float)$v>=$args[0])
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' > '.$args[0]];
  }

  public static function sd_forbid($v,$k)
  {
    if(!in_array($v,$k))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' not '.$v];
  }

  //url
  public static function is_url($v)
  {
	  if(preg_match('#(http|https)://(.*\.)?.*\..*#i',$v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid URL'];
  }

  //url
  public static function is_page($v)
  {
	  if(preg_match('/^[\d]+$/',$v) && $v!="0")
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' not able to be Zero'];
  }

  //id
  public static function is_id($v)
  {
	  if(preg_match('/^[\d]+$/',$v) && $v!="0")
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' not able to be Zero'];
  }

  //ipv4
  public static function is_ipv4($v)
  {
	  if(preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/',$v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid IP'];
  }

  //是否整型，强制转换
  public static function is_integer($v)
  {
	  if(preg_match('/^[\d]+$/',$v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid Integer'];
  }

  //有效实数
  public static function is_digits($v)
  {
	  if(is_numeric($v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid Digits'];
  }

  //有效日期
  public static function is_date(string $v)
  {
	  if(preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/',$v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid Date'];
  }

  //是否数组
  public static function is_array($v)
  {
    if(is_array($v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid Array'];
  }

  public static function is_string($v)
  {
    if(!is_array($v))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid String'];
  }

  //是否邮件
  public static function is_email($v)
  {
    if(filter_var($v,FILTER_VALIDATE_EMAIL))
    return ['code'=>SUCCESS];
    return ['code'=>REFUSE,'message'=>' Invalid Email'];
  }

  //是否json格式字符串，并转换
  public static function is_json($v,$k){
    if(is_string($v)){
      @$this->$request[$k]=json_decode($v);
      if(json_last_error() === JSON_ERROR_NONE)
      return ['code'=>SUCCESS];
    }
    return ['code'=>REFUSE,'message'=>' Invalid Json'];
  }

  public static function __callstatic($name,$args)
  {
    return ['code'=>REFUSE,'message'=>' Blocked By Undefined Validator Method '.$name];
  }
}
