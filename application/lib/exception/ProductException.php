<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 12:09
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code=404;
    public $msg='指定商品不存在，请检查参数';
    public $errorCode=30000;
}