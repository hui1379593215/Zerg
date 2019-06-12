<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/10
 * Time: 20:24
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code=403;
    public $msg='权限不够';
    public $errorCode=10001;
}