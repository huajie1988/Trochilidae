<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1/001
 * Time: 12:48
 */

namespace Trochilidae\bin\Lib\Route;


use Trochilidae\bin\Core\Route;

class RouteByJson extends Route implements RouteFactoryInterface
{
    public static function create($config){
        // TODO: Implement create() method.
        $routeFile=APP.'/'.$config->resource;

        if (!is_file($routeFile)){
            throw new \Exception('The route file'.$config->resource.' not find');
        }

        $routeReals=json_decode(file_get_contents($routeFile));
        $routes=[];
        foreach ($routeReals as $routeRealKey=>$routeReal) {
            $routeReal->path=str_replace('//','/',$config->prefix.$routeReal->path);
            $routes[$routeReal->path]=[
                'method'=>strtoupper($routeReal->method),
                'name'=>$routeRealKey,
                'target'=>$routeReal->target,
            ];
        }

        return $routes;
    }
}