<?php
//变量重置
$GLOBALS["DEFENDERS"]=[];
foreach (array_keys(Router::$routers) as $method)
unset($GLOBALS[$method]);

//加载中间层
LOAD_FILE('app/Http/Defends',APP_PATH);

//加载路由器
LOAD_FILE('app/Http/Routers',APP_PATH);

//加载控制器
LOAD_FILE('app/Http/Controllers',APP_PATH);

//加载模型层
LOAD_FILE('app/Models',APP_PATH);

//加载任务层
LOAD_FILE('app/Task',APP_PATH);

//加载websocket层
LOAD_FILE('app/Websocket',APP_PATH);

//销毁变量
unset($GLOBALS["DEFENDERS"]);
