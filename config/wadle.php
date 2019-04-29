<?php
//除了post，get之外允许其他请求方式
define('HTTP_METHOD',['__UPDATE__'=>'UPDATE','__DELETE__'=>'DELETE','__INSERT__'=>'INSERT','__RESET__'=>'RESET','__PUT__'=>'PUT','__ATTRIBUTE__'=>'ATTRIBUTE','__RECEIVE__'=>'RECEIVE','__CONFIRM__'=>'CONFIRM']);
define('HOST_NAME',shell_exec('hostname -i'));
define('WEB_NAME','http://'.HOST_NAME);
define('SWOOLE_PORT',80);
define('SWOOLE_LISTEN','0.0.0.0');
?>
