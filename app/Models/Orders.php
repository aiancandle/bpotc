<?php
namespace APP\Models;
class Orders extends \Model
{
  public $table='orders';
  public $output=['id','pickupUrl','receiveUrl','signType','orderNo','orderAmount','orderCurrency','customerId','create_time','state','audit_time','callback_result','name','callback_content','callback_time','confirm_time','callback_times','card_owner','equal_dollar'];
}
?>
