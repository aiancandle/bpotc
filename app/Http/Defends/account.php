<?php
namespace APP\Http\Defends;
use \Defend;
Abstract class Account
{
  static function update($data)
  {
    return validate(at_least_exist(accept($data,['BANK_NAME','CARD_NUMBER','CARD_NUMBER','id']),['BANK_NAME','CARD_NUMBER','CARD_NUMBER']),[
      'BANK_NAME'=>'lenth:6,60',
      'CARD_NUMBER'=>'lenth:6,60|integer',
      'CARD_OWNER'=>'lenth:6,60',
      'id'=>'id|required'
    ]);
  }

  static function insert($data)
  {
    return validate(accept($data,['BANK_NAME','CARD_NUMBER','CARD_OWNER']),[
      'BANK_NAME'=>'lenth:6,60|required',
      'CARD_NUMBER'=>'lenth:6,60|integer|required',
      'CARD_OWNER'=>'lenth:6,60|required',
    ]);
  }

  static function delete($data)
  {
    return validate($data,[
      'id'=>'id',
    ]);
  }

}

Defend::group(Account::class,function(){
    Defend::register('account.update','update');
    Defend::register('account.insert','insert');
    Defend::register('account.delete','delete');
});
