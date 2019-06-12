<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/9
 * Time: 22:25
 */

namespace app\lib\exception;

//参数错误异常
class ParameterException extends BaseException
{
    public $code=400;
    public $msg='参数错误';
    public $errorCode=10000;

}