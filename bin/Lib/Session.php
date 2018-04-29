<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29/029
 * Time: 22:02
 */

namespace Trochilidae\bin\Lib;


class Session
{
    public static function set($name,$value){
        session_start();
        $_SESSION[$name]=$value;
    }

    public static function get($name){
        session_start();
        return $_SESSION[$name];
    }

    public static function del($name){
        session_start();
        unset($_SESSION[$name]);
    }
}