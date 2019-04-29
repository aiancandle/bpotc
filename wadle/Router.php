<?php
class Router
{
  public static $prefix;
  public static $namespace;
  public static $middleware;
  public static $newed=0;
  public static $routers=['POST'=>null,'GET'=>null,'UPDATE'=>null,'DELETE'=>null,'INSERT'=>null,'RESET'=>null,'PUT'=>null,'ATTRIBUTE'=>null,'RECEIVE'=>null,'CONFIRM'=>null];
  public $uri;
  public $method;
  public $desitination;

  public function __construct($uri=null,$method=null,$desitination=null)
  {
    $this->uri=$uri;
    $this->method=$method;
    $this->desitination=$desitination;
  }

  //判断只想类型类型
  public static function desitinate($location)
  {
    list($class,$method)=explode("@",$location,2);
    return $class=="View"?["type"=>"View","to"=>$method]:["type"=>"Controller","to"=>["class"=>'\APP\Http\Controllers\\'.$class,"method"=>$method]];
  }

  //故名思意
  public static function post($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function get($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function update($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function reset($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function delete($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function insert($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function put($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function attribute($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function receive($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  public static function confirm($uri,$desitination)
  {
    $method=strtoupper(__FUNCTION__);
    $uri=self::$prefix?($uri==''?self::$prefix:self::$prefix.'/'.$uri):'/'.$uri;
    if(self::$namespace)$desitination=self::$namespace.'\\'.$desitination;
    $location=self::desitinate($desitination);
    self::$routers[$method][$uri][$location['type']]=$location['to'];
    $newone=new self($uri,$method,$desitination);
    if(self::$middleware)$newone->middleware(self::$middleware);
    return $newone;
  }

  //中间件
  public function middleware($middleware,callable $group=null)
  {
    $middleware=(array)$middleware;
    foreach ($middleware as &$value){
      if(isset($GLOBALS["DEFENDERS"][$value]))
      {
        $value=$GLOBALS["DEFENDERS"][$value];
        list($class,$method)=explode("@",$value,2);
        $value=["class"=>$class,"method"=>$method];
      }else{
        throw new Exception("不存在的中间件 ".$value."\n");
      }
    }
    if(isset(self::$routers[$this->method][$this->uri]["Defender"])){
      self::$routers[$this->method][$this->uri]["Defender"]=array_merge(self::$routers[$this->method][$this->uri]["Defender"],$middleware);
    }else{
      self::$routers[$this->method][$this->uri]["Defender"]=$middleware;
    }
    return $this;
  }

  //路由分组
  public function group(array $methods,callable $group)
  {
    self::$newed=1;
    if(isset($methods['prefix']))self::$prefix=self::$prefix?self::$prefix.'/'.$methods['prefix']:'/'.$methods['prefix'];
    if(isset($methods['namespace']))self::$namespace=self::$namespace?self::$namespace.'.'.$methods['namespace']:$methods['namespace'];
    if(isset($methods['middleware']))self::$middleware=$methods['middleware'];
    $group();
    self::$newed=0;
    return new self;
  }

  //前缀分组
  public function prefix($prefix,callable $group=null)
  {
    self::$newed=1;
    self::$prefix=self::$prefix?self::$prefix.'/'.$prefix:'/'.$prefix;
    if($group){$group();self::$newed=0;}
    return new self;
  }

  //命名空间分组
  public function namespace($namespace,callable $group=null)
  {
    self::$newed=1;
    self::$namespace=self::$namespace?self::$namespace.'.'.$namespace:$namespace;
    if($group){$group();self::$newed=0;}
    return new self;
  }

  public static function load()
  {
    foreach (self::$routers as $method => $uri)$GLOBALS[$method]=isset($GLOBALS[$method])?array_merge($GLOBALS[$method],$uri):$uri;
  }

  //销毁示例初始化
  public function __destruct()
  {
    if(self::$newed==0)
    self::$prefix=self::$namespace=self::$middleware=null;
  }
}

?>
