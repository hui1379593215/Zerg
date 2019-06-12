<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/11
 * Time: 22:15
 */

namespace app\api\controller;

use \app\api\service\Token as TokenServer;
use think\Controller;

class BaseController extends Controller
{
    protected function checkExclusiveScope(){

        TokenServer::needExculsiveScope();
    }


    protected function checkPrimaryScope(){

       TokenServer::needPrimaryScope();

    }

}