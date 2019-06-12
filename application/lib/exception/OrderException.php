<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/14
 * Time: 20:39
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code=404;
    public $msg='订单不存在请检查ID';
    public $errorCode=80000;

}