<?php
	namespace Trochilidae\bin;
    use Monolog\Handler\StreamHandler;
    use Monolog\Logger;
    use Trochilidae\bin\Core\Controller;
    use Trochilidae\bin\Core\Route;
    use Whoops\Handler\PrettyPageHandler;
    use Whoops\Run;

    /**
	* 
	*/
	class Trochilidae
	{
		public static function run()
		{
                $storagePath=TROCHI.'/storage/log/'.date('Y/m/d');
                if(!is_dir($storagePath)){
                    mkdir($storagePath,0777,true);
                }

		        if(DEBUG){
                    //注册异常处理
                    $whoops = new Run();
                    $whoops->pushHandler(new PrettyPageHandler());
                    $whoops->register();
                    $start_time = microtime(true);
                    $logger=new Logger('Main process');
                    $logger->pushHandler(new StreamHandler($storagePath.'/'.date('YmdH').'.log', Logger::INFO));
                    $logger->addInfo('Main process start');
                    self::process($logger);
                    $end_time = microtime(true);

                    $logger->addInfo('Main process stop',['executionTime(ms)'=>($end_time-$start_time)*1000]);
                }else{
		            try{
                        self::process();
                    }catch (\Exception $e){
                        $logger=new Logger('Main process');
                        $logger->pushHandler(new StreamHandler($storagePath.'/'.date('YmdH').'.log', Logger::ERROR));
                        $logger->addError($e);
                    }
                }
		}

		private static function process(){
            $route=new Route();
            $routes=$route->getAction();
            $controller=new Controller();
            $controller->__run($routes['classFileName'],$routes['methodName'],$route);

        }

	}