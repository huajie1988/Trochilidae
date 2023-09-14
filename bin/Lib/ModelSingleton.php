<?php

namespace Trochilidae\bin\Lib;

use Medoo\Medoo;
class ModelSingleton{
    private static $instance;

    private function __construct(){

    }

    private function __clone(){
        // TODO: Implement __clone() method.
    }

    public static function getInstance($option){
        if (!(self::$instance instanceof Medoo)) {
            self::$instance = new Medoo($option);
        }
        return self::$instance;
    }
}