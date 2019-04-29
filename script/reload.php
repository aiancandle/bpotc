<?php
class Server {
  const PORT = 80;

  public function port() {
    $shell = "ps aux | grep live_master | grep -v grep";
    $result=shell_exec($shell);
    if(!preg_match("'live_master'",$result))
    {
      echo date("YmdHis").PHP_EOL;
      shell_exec("/usr/local/php/bin/php /web/index.php");
    }
  }
}

swoole_timer_tick(2000,function(){
  (new Server())->port();
});

// nohup /usr/local/php/bin/php /web/script/reload.php > /web/script/reload.log &
// docker run -p 80:80 -it -v /d/swoole:/web  swoole      /bin/bash /usr/local/php/bin/php /web/script/reload.php
// /usr/local/php/bin/php /web/index.php

//mac
// docker run -p 80:80 -itv  /Users/tanglingjuan/Desktop/docker:/web  swoole
