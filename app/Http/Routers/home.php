<?php
Router::get("test","Trader@test");
Router::get("","Admin@index");
Router::post("","Trader@attribute")->middleware("trader.attribute");
Router::receive("","Trader@receive")->middleware("trader.receive");
Router::confirm("","Trader@confirm")->middleware("trader.confirm");

Router::attribute("","admin@login")->middleware('admin.login');
Router::get("logout","admin@logout");

Router::group(['middleware'=>'admin.logined'],function(){
  Router::get('time',"Admin@time");
  Router::post('auditOrder',"Admin@audit")->middleware(['admin.audit','admin.confirm']);
  Router::post('callbackOrder',"Admin@callback")->middleware('admin.callback');
  Router::post('newConfigs',"Admin@newConfigs")->middleware(['admin.confirm','admin.configs']);
  Router::post('getConfigs',"Admin@getConfigs")->middleware(['admin.confirm']);
  Router::get('cashManagement',"Admin@cashManagement")->middleware('admin.page');
  Router::get('callbackLog',"Admin@callbackLog")->middleware('admin.page');
  Router::get("getExcel","Admin@excel");
  Router::post("reset","Admin@reset")->middleware('admin.confirm');
  Router::post("hardRestart","Admin@hardRestart")->middleware('admin.confirm');
  Router::post("softRestart","Admin@softRestart")->middleware('admin.confirm');
  Router::get("orderDetails","Admin@orderDetails");

  Router::get("accounts","Account@index");
  Router::post("accounts","Account@insert")->middleware(['admin.confirm','account.insert']);
  Router::put("accounts","Account@update")->middleware(['admin.confirm','account.update']);
  Router::delete("accounts","Account@delete")->middleware(['admin.confirm','account.delete']);

});
Router::get("abc","Admin@hardRestart");
Router::post("setConfigs","Admin@install")->middleware(['admin.configs','admin.install']);


Router::load();


?>
