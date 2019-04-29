<?php
abstract class Model implements Iterator
{
  public $pdo;
  public $table;//表名
  public $id;//记录id
  private $data;//赋值数据
  private $tmp;//查询记录缓存
  public $output=[];
  public $input=[];

  public function __construct($id=null)
  {
    //$this->pdo= new \PDO ('mysql:host='.MYSQL_HOST.';dbname='.MYSQL_DBNAME,MYSQL_USER,MYSQL_PASSWORD);
    $this->pdo=new \PDO('sqlite:/web/data/money.db');
    $this->input=array_merge($this->output,$this->input);
    if($id!==null)$this->id=$id;
  }

  public function __set($name, $value)
  {
    if(in_array($name,$this->input))
    $this->data[$name]=$value;
  }

  public function __get($name)
  {
    if(empty($this->tmp[$this->id]) && in_array($name,$this->input))
    {
      if(empty($this->id))return;
      $this->tmp[$this->id]=$this->sqlres($this->id,true);
    }
    return $this->tmp[$this->id][$name];
  }

  public function output()
  {
    if(empty($this->tmp[$this->id]))
    {
      if(empty($this->id))return;
      $this->tmp[$this->id]=$this->sqlres($this->id);
    }
    return (object)array_intersect_key($this->tmp[$this->id],array_flip($this->output));
  }

  //插入数据
  public function insert($data=null)
  {
    $data=$data==null?$this->data:(array)$data;
    $fields=$binds=null;
    foreach ($data as $k => $v){
      $fields.=','.$k;
      $binds.=',:'.$k;
    }
    $fields=ltrim($fields,',');
    $binds=ltrim($binds,',');
    $stmt = $this->pdo->prepare('INSERT INTO '.$this->table.' ('.$fields.') values ('.$binds.') ');

    $stmt->execute($data);
    $rc=$stmt->rowCount();
    if($rc)$rc=$this->pdo->lastInsertId();
    return $rc;
  }

  //更新数据
  public function update($data=null)
  {
    $data=$data==null?$this->data:(array)$data;
    $binds=null;
    foreach ($data as $k => $v)
    $binds.=','.$k.'=:'.$k;
    $binds=ltrim($binds,',');
    $stmt = $this->pdo->prepare('UPDATE '.$this->table.' SET '.$binds.' WHERE id = '.$this->id);
    $stmt->execute($data);
    $rc=$stmt->rowCount();
    return $rc;
  }

  //删除数据
  public function delete($id=null)
  {
    $id=$id?$id:$this->id;
    $rc=$this->pdo->exec('DELETE FROM '.$this->table.' WHERE id = '.$id);
    return $rc;
  }

  public function exec($sql)
  {
    return $this->pdo->exec($sql);
  }

  private $limits=[];

  public function in($table,$id_name,$bind_id,$this_id=null)
  {
    $bind_id=is_int($bind_id)?$bind_id:'\'{$bind_id}\'';
    $this_id=$this_id??substr($this->table,0,1).'id';
    $this->limits['in']=' id in (SELECT '.$this_id.' FROM '.$table.' WHERE '.$id_name.' = '.$bind_id.') ';
    return $this;
  }

  public function locate(array $array)
  {
    foreach ($array as $key => &$value)
    $value=$key.'=\''.$value.'\'';
    $this->limits['locate']=implode(' and ',$array);
    return $this;
  }

  public function avoid(array $array)
  {
    foreach ($array as $key => &$value)
    $value=$key.'!=\''.$value.'\'';
    $this->limits['locate']=implode(' and ',$array);
    return $this;
  }

  public function or(array $array)
  {
    foreach ($array as $key => &$value)
    $value=$key.'=\''.$value.'\'';
    $this->limits['or']='( '.implode(' or ',$array).' )';
    return $this;
  }

  //返回符合条件的一个
  public function catch()
  {
    $data=$this->sqlres();
    if(count($data)>0)
    {
      $data=(object)$data[0];
      $this->id=$data->id;
      return $data;
    }
    return null;
  }

  public function between(array $array)
  {
    $tmp;
    foreach ($array as $key => &$value)
    {
      $value=explode('-',$value);
      if($value[0])$tmp[]=$key.'>'.$value[0];
      if($value[1])$tmp[]=$key.'<'.$value[1];
    }
    $this->limits['between']=implode(' and ',$tmp);
    return $this;
  }

  public function like(array $array)
  {
    foreach ($array as $key => &$value)
    $value=$key.' LIKE \'%'.$value.'%\'';
    $this->limits['like']=implode(' or ',$array);
    return $this;
  }

  public function regexp(array $array)
  {
    foreach ($array as $key => &$value)
    $value=$key.' REGEXP \''.$value.'\' ';
    $this->limits['regexp']=implode(' and ',$array);
    return $this;
  }

  public function combine()
  {
    $combine=[];
    $return='';
    $adds=['in','locate','between','like','regexp','or','avoid'];
    foreach ($adds as $add)
    if($this->{$add})$combine[]=$this->{$add};
    if($combine)$return='WHERE '.implode(' and ',$combine);
    return $return;
  }

  public function union($table)
  {
    $this->limits['union']='';
    return $this;
  }

  //数据结果
  public $total;
  private function sqlres($id=null,$input=false)
  {
    $binds=implode(',',$input?$this->input:$this->output);
    if($id!==null)
    {
      return $this->pdo->query('SELECT '.$binds.' FROM '.$this->table.' WHERE id = '.$id.' LIMIT 1')->Fetch(\PDO::FETCH_ASSOC);
    }else{
      $ands='';
      if($this->limits)$ands=' WHERE '.implode(' and ',$this->limits);
      if($this->paginations)
      {
        $start=$this->paginations['pageSize']*($this->paginations['currentPage']-1);
        $total=$this->pdo->query('SELECT count(id) as pages FROM '.$this->table.$ands)->Fetch(\PDO::FETCH_ASSOC)['pages'];
        $this->total=$total;
        $ands.=' limit '.$start.','.$this->paginations['pageSize'];
      }
      return $this->pdo->query('SELECT '.$binds.' FROM '.$this->table.$ands)->FetchAll(\PDO::FETCH_ASSOC);
    }
  }

  public function fetchAll($sql)
  {
    return $this->pdo->query($sql)->FetchAll(\PDO::FETCH_ASSOC);
  }

  public function fetch($sql)
  {
    return $this->pdo->query($sql)->Fetch(\PDO::FETCH_ASSOC);
  }

  //检查是否存在
  public function exist($value,$name='id')
  {
    return $this->pdo->query('SELECT count(*) as num FROM '.$this->table.' WHERE `'.$name.'`= \''.$value.'\'')->Fetch(\PDO::FETCH_ASSOC)['num'];
  }

  //显示数组结果
  public function array($bool=false)
  {
    $result=$this->sqlres($this->id??null);
    if($this->attached_)$result['attached_']=$this->attached_;
    return $result;
  }

  public $attached_;
  //关联查询
  public function attach($table)
  {
    $id_name=substr($this->table,0,1).'id';
    $this->attached_=$this->fetchAll('SELECT * FROM '.$table.' WHERE `'.$id_name.'`='.$this->id);
    return $this;
  }

  private $paginations=[];
  public function paginate($currentPage,$pageSize=6)
  {
    $str=$this->table;
    $this->paginations=compact('currentPage','pageSize');
    return $this;
  }

  public function execute()
  {
    if($this->data && $this->id){
      $rc=$this->update();
      if($rc && count($this->tmp[$this->id])>0){
        foreach ($this->data as $k => $v)
        {
          if(isset($this->tmp[$this->id][$k]))$this->tmp[$this->id][$k]=$v;
        }
      }
      return $rc;
    }elseif($this->data){
      return $this->insert();
    }
  }

  public function __invoke(int $id)
  {
    if(empty($this->tmp[$id]))$this->tmp[$id]=$this->sqlres($id);
    return (object)$this->tmp[$id];
  }

  //迭代器
  private $position = 0;//定位
  private $array;

  function rewind(){
    $this->array=$this->sqlres();
    $this->limits=[];
  }

  function current(){
    return (object)$this->array[$this->position];
  }

  function key(){
    return $this->position;
  }

  function next(){
    ++$this->position;
  }

  function valid(){
    return isset($this->array[$this->position]);
  }
}
