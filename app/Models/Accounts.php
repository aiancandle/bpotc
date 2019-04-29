<?php
namespace APP\Models;
class Accounts extends \Model
{
  public $table='accounts';
  public $output=['id','BANK_NAME','CARD_NUMBER','CARD_OWNER'];
}
?>
