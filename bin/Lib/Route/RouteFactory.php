<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1/001
 * Time: 12:55
 */

namespace Trochilidae\bin\Lib\Route;


class RouteFactory implements RouteFactoryInterface
{
    private static $class;
    public function __construct($className){
        $className=ucfirst($className);
        $class='Trochilidae\bin\Lib\Route\RouteBy'.$className;
        if(!class_exists($class)){
            throw new \Exception('The class '.$class.' not find');
            exit();
        }

        self::$class=$class;
    }

    public static function create($config){
        // TODO: Implement create() method.
        $c=self::$class;
        return $c::create($config);
    }

}