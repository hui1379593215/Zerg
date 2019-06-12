<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/7
 * Time: 11:01
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code=404;
    public $msg='用户不存在';
    public $errorCode=60000;
}