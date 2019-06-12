<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/27
 * Time: 23:51
 */

namespace app\api\validate;


/**
 * Class IDCollection
 * @package app\api\validate
 * 验证Theme传入的数组判断是不是正确的
 */
class IDCollection extends BaseValidate
{
    protected $rule = [
      'ids'=>'require|checkIDs'
    ];

    protected $message=[
        'ids'=>'ids参数必须为以逗号分隔的多个正整数'
    ];

    //客户端传递过来的ids=id1,id2,id3
    protected function checkIDs($value)
    {
        $value= explode(',',$value);

        //判断传进来进来的数组是不是空的
        if(empty($value))
        {
            return false;
        }

        foreach ($value as $id)
        {
            if(!$this->isPositiveInteger($id))
            {
                return false;
            }
            return true;
        }
    }
}