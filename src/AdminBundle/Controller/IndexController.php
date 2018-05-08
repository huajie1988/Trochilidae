<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/1/001
 * Time: 13:25
 */
namespace AdminBundle\Controller;

use Trochilidae\bin\Core\Controller;
use Trochilidae\bin\Core\Route;

class IndexController extends Controller
{
    /**
     * @Route(path="/",method="POST")
     */
    public function indexAction(){
        print_r('is Admin');
    }

    /**
     * @Route(path="/test/id/\d+",method="GET")
     */
    public function testAction(Route $route){
        print_r('is Admin Test'.$route->get('id'));
    }
}