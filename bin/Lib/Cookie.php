<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29/029
 * Time: 22:01
 */

namespace Trochilidae\bin\Lib;


use Trochilidae\bin\Core\Config;

class Cookie
{
    public static function set($name,$value,$options=[]){
        $expire=time()+Config::getOneConfig('cookie_expire','site');
        $path=Config::getOneConfig('cookie_path','site');
        $domain=$_SERVER['HTTP_HOST'];
        $name=Config::getOneConfig('cookie_prefix','site').$name;
        foreach ($options as $option=>$value) {
            $$option=$value;
        }
        setcookie($name,$value,$expire,$path,$domain);
    }

    public static function get($name){
        $name=Config::getOneConfig('cookie_prefix','site').$name;
        return $_COOKIE[$name];
    }

    public static function del($name){
        $name=Config::getOneConfig('cookie_prefix','site').$name;
        setcookie($name,'',time()-1);
    }
}