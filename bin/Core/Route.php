<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 0:44
 */

namespace Trochilidae\bin\Core;


use Trochilidae\bin\Common\Utils;
use Trochilidae\bin\Lib\Http;
use Trochilidae\bin\Lib\Route\RouteFactory;

class Route
{
   private $method="GET";
   private $pathInfo="/";
   private $_get=[];
   private $_post=[];
   private $_server=[];
   private $_header=[];
   private $_json=[];
   public function __construct(){
       $this->method=$_SERVER['REQUEST_METHOD'];
       $this->_get=$_GET;
       $this->_post=$_POST;
       $this->_server=$_SERVER;
       $this->_header=$this->getHeader();
       $this->_json=$this->getJSON();
       if(isset($_SERVER['PATH_INFO'])){
           $this->pathInfo=$_SERVER['PATH_INFO'];
           $pathInfo=explode('/',trim($this->pathInfo,'/'));
           for($i=count($pathInfo)-1;$i>=0;$i-=2){
               if(isset($pathInfo[$i-1])){
                   $this->_get[$pathInfo[$i-1]]=$pathInfo[$i];
               }
           }
       }

   }

   public function getRouteTable(){
        $storagePath=TROCHI.'/storage/framework/';

        if(!is_dir($storagePath)){
            mkdir($storagePath,0777,true);
        }
        $fileName=$storagePath.'routes.json';
        if(!is_file($fileName) || DEBUG){
            $configs=Config::getConfig('routes');
            $routes=[];
            foreach ($configs as $config) {
                new RouteFactory($config->type);
                $routes = array_merge($routes,RouteFactory::create($config));
            }
            file_put_contents($fileName,json_encode($routes,JSON_UNESCAPED_SLASHES));
        }

        $routeTable=file_get_contents($fileName);
        return json_decode($routeTable);
   }

   public function getPathInfo(){
       return $this->pathInfo;
   }

   public function getAction(){
       $routeJson=(array)$this->getRouteTable();
       $routeKeys=array_keys($routeJson);
       $found=false;
       $methodName='';
       $classFileName='';
       foreach ($routeKeys as $routeKey) {
            if(preg_match("!^$routeKey$!",$this->pathInfo)){

                $route=$routeJson[$routeKey];
                if($this->method==$route->method){
                    $targets=$route->target;
                    list($methodName,$classFileName)=Utils::explodeStringBySymbol($targets);
                    $found=true;
                    break;
                }
            }
       } 

       if(!$found){
           if(DEBUG){
               throw new \Exception('The route '.$this->method.':'.$this->pathInfo.' not find');
           }else{
                $http_exception_path=Config::getOneConfig('http_exception_path','site');
                $status='404';

                if(trim($http_exception_path)=='' || $http_exception_path==null){
                    $path=BIN.'/Lib/Http/views';
                }else{
                    $path=APP.$http_exception_path;
                }

                $tpl=$path.'/'.$status.'.html.twig';
                $controller=new Controller();
                $view=$controller->renderOnly($tpl,['method'=>$this->method,'pathInfo'=>$this->pathInfo],true);
                Http::response($status,$view);
                exit();
           }
       }

       return [
            'classFileName'=>$classFileName,
            'methodName'=>$methodName
       ];

   }

    function getHeader() {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            }
            if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $header['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
            } elseif (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                $header['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
            }
            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $header['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
            }
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $header['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
            }
        }
        return $headers;
    }

   public function getJSON($isArray=true){
       $res=false;

       if(isset($_SERVER['CONTENT_TYPE']) && strtolower($_SERVER['CONTENT_TYPE'])=='application/json'){
           $res=json_decode(file_get_contents('php://input'),$isArray);
       }
       return $res;
   }

   private function __getParams($list,$name='',$fitter='string',$default=''){
       if(trim($name)==''){
           return $list;
       }
       $instance=Utils::getInstance();
       return isset($list[$name])?$instance::fitter($list[$name],$fitter):$default;

   }

   public function request($name='',$fitter='string',$default=''){
       $request=array_merge($this->_get,$this->_post);

       return $this->__getParams($request,$name,$fitter,$default);
    }

   public function get($name='',$fitter='string',$default=''){
       return $this->__getParams($this->_get,$name,$fitter,$default);
   }

    public function post($name='',$fitter='string',$default=''){
        return $this->__getParams($this->_post,$name,$fitter,$default);
    }

    public function header($name='',$fitter='string',$default=''){
        return $this->__getParams($this->_header,strtoupper($name),$fitter,$default);
    }

    public function json($name='',$fitter='string',$default=''){
        return $this->__getParams($this->_json,$name,$fitter,$default);
    }

}