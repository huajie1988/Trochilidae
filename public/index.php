<?php

	// 检测PHP环境
	if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');

    date_default_timezone_set( 'Asia/Shanghai' );

	define('TROCHI', realpath('../'));
	define('BIN', TROCHI.'/bin');
	define('APP', TROCHI.'/src');

	define('DEBUG', (preg_match('!(^127\.)|(^localhost)!', $_SERVER['HTTP_HOST'])));
//    define('DEBUG', false);

// 可能需要做其他处理，故保留if-else
	if(DEBUG){
		ini_set('display_error', 'On');
	}else{
		ini_set('display_error', 'Off');
	}
	
	require '../vendor/autoload.php';

	include_once BIN.'/Trochilidae.php';

	Trochilidae\bin\Trochilidae::run();
