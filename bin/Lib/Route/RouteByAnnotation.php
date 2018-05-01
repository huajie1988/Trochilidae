<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1/001
 * Time: 12:48
 */

namespace Trochilidae\bin\Lib\Route;


use Trochilidae\bin\Core\Route;
use Trochilidae\bin\Lib\Ioc;

class RouteByAnnotation extends Route implements RouteFactoryInterface
{
    public static function create($config){

        // TODO: Implement create() method.
        $routeDir=APP.'/'.$config->resource;

        if (!is_dir($routeDir)){
            throw new \Exception('The route dir'.$config->resource.' not find');
        }
        $routes=[];
        foreach(glob($routeDir.'/*') as $filename)
        {
            $className=str_replace([APP.'/','.php'],'',$filename);
            $target=$className;
            $className=str_replace('/','\\',$className);
            $reflection=new \ReflectionClass($className);
            $methods=$reflection->getMethods();
            foreach ($methods as $method) {
                $routeInfo=[
                    'method'=>'GET',
                    'path'=>''
                ];
                if($method->isPublic()){
                    $doc=$method->getDocComment();
                    preg_match('!@Route\((.+)\)!',$doc,$match);

                    if(isset($match[1])){
                        $routeText=$match[1];
                        $routeTextArr=explode(',',$routeText);
                        foreach ($routeTextArr as $routeTextItem) {
                            preg_match('!(\w+)=[\'"](.+)[\'"]!',$routeTextItem,$match);
                            if(isset($match[1]) && isset($match[2]))
                                $routeInfo[$match[1]]=$match[2];
                        }
                        if(trim($routeInfo['path'])=='') break;
                        $path=str_replace('//','/',$config->prefix.$routeInfo['path']);
                        $routes[$path]=[
                            'method'=>strtoupper($routeInfo['method']),
                            'name'=>str_replace(['\\','/'],'_',strtolower($className)).'_'.$method->getName(),
                            'target'=>$method->getName().'@'.$target,
                        ];
                    }
                }
            }
        }


        return $routes;
    }
}