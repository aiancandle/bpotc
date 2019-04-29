<?php
namespace APP\Models;
use \APP\Http\Defends\Admin;
class Configs extends \Model
{
  public $table='configs';
  // public $output=['id','role','last','ip','online','late','server','name','area','login','platform'];
  //public $input=['password'];
  public $output=['LOGIN_ACCOUNT','LOGIN_PASSWORD','CONFIRM_PASSWORD','EMAIL_SENDER','LEANWOREK_KEY','SERVER_OPEN','MAX_VALUE','MIN_VALUE','EMAIL_PASSWORD','EMAIL_SUBJECT','EMAIL_RECEIVER','AUTO_CALLBACK','EXCHANGE_RATE','COMPANY_ABBREVIATION','COMPANY_NAME'];
}
// $pdo = new \PDO('sqlite:/web/data/money.db');
// $pdo->exec("CREATE TABLE IF NOT EXISTS user (
//         id INTEGER PRIMARY KEY,
//         name TEXT,
//         time TEXT)");
?>
