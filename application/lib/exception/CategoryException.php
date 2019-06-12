<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 13:23
 */

namespace app\lib\exception;


use app\api\model\BaseModel;

class CategoryException extends BaseModel
{
    public $code=500;
    public $msg='指定类目不存在,请检查参数ID';
    public $errorCode=50000;
}