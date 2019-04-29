<?php
function dater()
{
  return date('Y-m-d H:i:s',time());
}

//返回视图层
function view($name,$data=null)
{
  $name=str_replace(".","/",$name);
  $content=file_get_contents(APP_PATH."/front/view/".$name.".html");
  if($data){
    $parttern=$replace=[];
    foreach ($data as $key => $value) {
      $parttern[]="'{\s*{\s*{$key}\s*}\s*}'";
      $replace[]=$value;
    }
    $content=preg_replace($parttern,$replace,$content);
  }
  return $content;
}

//api响应
function code($code,$message=null,$data=null)
{
  $return=["code"=>$code];
  if($message)$return["message"]=$message;
  if($data)$return["data"]=$data;
  return json_encode($return);
}

//响应
function response($code,$message=null,$data=null)
{
  $return=["code"=>$code];
  if($message)$return["message"]=$message;
  if($data)$return["data"]=$data;
  return (object)$return;
}

//生成随机session
function session($salt="aiancandle")
{
  return md5(chr(rand(65,90)).chr(rand(65,90)).$salt.chr(rand(65,90)).chr(rand(65,90)));
}

//调用表单验证
function validate($data,$params)
{
  //print_r($params);
  if($data->code!=SUCCESS)return $data;
  foreach ($params as $param => $items)
  {
    $limits=explode('|',trim($items,"|"));
    if(in_array('required',$limits))
    {
      $limits=array_diff($limits,['required']);
      if(!isset($data->request[$param]))
      {
        $checked=["code"=>REFUSE,"message"=>" Required"];
        goto HELL;
      }elseif(count($limits)>0){
        goto GOON;
      }else{
        continue;
      }
    }
    GOON:
    if(isset($data->request[$param])){
      foreach ($limits as $limit)
      {
        $tus=explode(":",$limit);
        $args=$tus[1]??null;
        if(isset($tus[1])){
          $checked=\Validator::{"sd_".$tus[0]}($data->request[$param],explode(",",$args));
        }else{
          if(isset($data->request[$param]))
          $checked=\Validator::{"is_".$tus[0]}($data->request[$param]);
        }
        if($checked["code"]!=SUCCESS)goto HELL;
      }
    }

  }
  return $data;
  HELL:
  $data->code=$checked["code"];
  $data->message=$param." is".$checked["message"];
  return $data;
}

function at_least_exist($data,$array,$number=1)
{
  if($data->code!=SUCCESS)return $data;
  if(count(array_intersect_key($data->request,array_flip($array)))<$number)
  {
    $data->code=REFUSE;
    $data->message=implode(',',$array).' Must Exist '.$number;
  }
  return $data;
}

//明确接受的参数
function accept($data,$accepts)
{
  if($data->code!=SUCCESS)return $data;
  if($data->request)$data->request=array_intersect_key($data->request,array_flip($accepts));
  return $data;
}

function store($file)
{
  $time=time();
  $date=date("Y/m/d",$time);
  $uploadPath = APP_PATH."/front/public/uploads/";
  $uploadUrl = "/uploads/";
  if(!is_dir($uploadPath))mkdir($uploadPath, 0777, true);
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if($ext == "php")return null;
  $img = uniqid() . mt_rand(1000, 9999) . '.' . $ext;
  $imgPath = $uploadPath. $img;
  $url = WEB_NAME. $uploadUrl . $img;
  if(move_uploaded_file($file['tmp_name'], $imgPath))
  {
    $content=$file['name'];
    if(in_array($ext,["bmp","jpg","jpeg","png","gif","svg"]))
    {
      $type="image";
    }else{
      $type="file";
    }
    return compact("content","type","url");
  }
  return null;
}

?>
