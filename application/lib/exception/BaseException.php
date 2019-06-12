<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/8
 * Time: 22:22
 */

namespace app\lib\exception;

//统一描述错误
use think\Exception;
use Throwable;

//让所有的异常继承Exception
class BaseException extends Exception
{
    //HTTP 状态码404 200
    public $code=400;

    //错误具体信息，最好定义英文的
    public $msg='参数错误';

    //自定义的错误码
    public $errorCode=999;

    //重写了各个类传参的值
    //子类想改变一些参数，可以调用父类__construct的构造方法
    public function __construct($params = [])
    {
        //判断传进来的数组是不是空的
        if(!is_array($params)) {
            return;
        }

        if (array_key_exists('code',$params)){
            $this->code=$params['code'];
        }

        if (array_key_exists('msg',$params)){
            $this->msg=$params['msg'];
        }

        if (array_key_exists('errorCode',$params)){
            $this->errorCode=$params['errorCode'];
        }
    }


}