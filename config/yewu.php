<?php
if(file_exists('/web/data/money.db')){
  $pdo=new \PDO('sqlite:/web/data/money.db');
  $array=$pdo->query('SELECT * FROM configs WHERE id = 1')->Fetch(\PDO::FETCH_ASSOC);
  unset($array['id']);
  foreach ($array as $k => $v) {
    if($k=='MAX_VALUE' || $k=='MIN_VALUE')
    {
      define($k,sprintf("%.2f", $v));
    }else{
      define($k,$v);
    }
  }

  $array=$pdo->query('SELECT * FROM accounts')->FetchAll(\PDO::FETCH_ASSOC);

  define('USER_ACCOUNTS',$array);
  define('USER_ACCOUNTS_NUM',count($array));

  unset($pdo);
  unset($array);
  unset($k);
  unset($v);
}
