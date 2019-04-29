<?php

//加载文件
function LOAD_FILE($file,$path)
{
  foreach (scandir($path.'/'.$file) as $file_)
  {
    if(is_dir($path.'/'.$file.'/'.$file_) && $file_!='.' && $file_!='..')
    {
      load($file_,$path.'/'.$file);
    }
    else
    {
      if(preg_match("'^[\w]+.php$'",$file_) && $file_!='Init.php')
      {
        require $path.'/'.$file.'/'.$file_;
      }
    }
  }
}

//加载各项参数
LOAD_FILE('config',APP_PATH);

//加载核心文件
LOAD_FILE('wadle',APP_PATH);

//加载工具包
require APP_PATH."/vendor/register.php";
?>
