<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1/001
 * Time: 12:53
 */

namespace Trochilidae\bin\Lib\Route;


interface RouteFactoryInterface
{
    public static function create($config);
}