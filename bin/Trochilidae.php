<?php
	namespace Trochilidae\bin;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Trochilidae\bin\Core\Controller;
    use Trochilidae\bin\Core\Route;
    use Trochilidae\bin\Lib\Aop;
    use Whoops\Handler\PrettyPageHandler;
    use Whoops\Run;

    /**
	* 
	*/
	class Trochilidae
	{
	    private static $logger;
		public static function run()
		{
                $storagePath=TROCHI.'/storage/log/'.date('Y/m/d');
                if(!is_dir($storagePath)){
                    mkdir($storagePath,0777,true);
                }
                self::$logger=new Logger('Main process');
		        if(DEBUG){
                    //注册异常处理
                    $whoops = new Run();
                    $whoops->pushHandler(new PrettyPageHandler());
                    $whoops->register();
                    $start_time = microtime(true);

                    self::$logger->pushHandler(new StreamHandler($storagePath.'/'.date('YmdH').'.log', Logger::INFO));
                    self::$logger->info('Main process start');
                    self::process();
                    $end_time = microtime(true);
                    self::$logger->info('Main process stop',['executionTime(ms)'=>($end_time-$start_time)*1000]);
                }else{
		            try{
                        self::process();
                    }catch (\Exception $e){
                        self::$logger->pushHandler(new StreamHandler($storagePath.'/'.date('YmdH').'.log', Logger::ERROR));
                        self::$logger->error($e);
                    }
                }
		}

		private static function process(){
            ob_start();


            if(DEBUG){
                $aop=new Aop(new Route(),self::$logger);
                $routes=$aop->getAction();
                $route=$aop->getObject();
                $aop=new Aop(new Controller(),self::$logger);
                $aop->__run($routes['classFileName'],$routes['methodName'],$route);
            }else{
                $route=new Route();
                $routes=$route->getAction();
                $controller=new Controller();
                $controller->__run($routes['classFileName'],$routes['methodName'],$route);
            }

            ob_end_flush();
        }

	}