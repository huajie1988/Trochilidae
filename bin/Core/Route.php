<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 0:44
 */

namespace Trochilidae\bin\Core;


use Trochilidae\bin\Common\Utils;
use Trochilidae\bin\Lib\Route\RouteFactory;

class Route
{
   private $method="GET";
   private $pathInfo="/";
   private $_get=[];
   private $_post=[];

   public function __construct(){
       $this->method=$_SERVER['REQUEST_METHOD'];
       $this->_get=$_GET;
       $this->_post=$_POST;
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
        if(!is_file($fileName)){
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
           throw new \Exception('The route '.$this->method.':'.$this->pathInfo.' not find');
       }

       return [
            'classFileName'=>$classFileName,
            'methodName'=>$methodName
       ];

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

}