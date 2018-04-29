<?php

	namespace Trochilidae\bin\Common;

	/**
	* 
	*/
	class Utils
	{
        private static $_instance=null;
		private function __construct(){
        }

        public static function getInstance(){
            if(!(self::$_instance instanceof Utils)){
                self::$_instance = new self;
            }
            return self::$_instance;
        }

        public static function fitter($value,$type){

		    switch ($type){
                case 'int':
                    $value=intval($value);
                    break;
                case 'float':
                    $value=floatval($value);
                    break;
                case 'raw':
                    break;
                case 'string':
                    $value=htmlspecialchars(strip_tags($value));
                    break;
                case 'bool':
                    $value=boolval($value);
                    break;
                default:
                    $value=htmlspecialchars(strip_tags($value));
            }

            return $value;
        }

        public static function convertUnderline ( $str , $ucfirst = true){
            $str = ucwords(str_replace('_', ' ', $str));
            $str = str_replace(' ','',lcfirst($str));
            return $ucfirst ? ucfirst($str) : $str;
        }

        public static function explodeStringBySymbol($string,$symbol='@'){
		        return explode($symbol,$string);
        }

	}