<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/26/026
 * Time: 10:27
 */

namespace Trochilidae\bin\Core;


class Config
{
    public static function getConfig($file="config",$extension="json"){
        $file=TROCHI.'/config/'.$file.'.'.$extension;

        if(!is_file($file)){
            throw new \Exception("The config file not find");
        }
        $config=file_get_contents($file);
        return json_decode($config);
    }

    public static function getOneConfig($field,$file="config",$extension="json"){
        $config=self::getConfig($file,$extension);

        if(isset($config->$field))
            return $config->$field;
        else
            return null;
    }
}