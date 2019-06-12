<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/8
 * Time: 22:29
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    //覆盖掉父类的参数

    //状态码
    public $code=400;
    //错误信息
    public $msg='请求Banner不存在';
    //自定义的错误信息
    public $errorCode=40000;

}