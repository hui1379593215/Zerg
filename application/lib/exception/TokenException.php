<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/6
 * Time: 20:40
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code=401;
    public $msg='Token过期或者无效';
    public $errorCode=10001;
}