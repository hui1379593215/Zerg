<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/7
 * Time: 13:21
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code=201;
    public $msg='ok';
    public $errorCode=0;
}