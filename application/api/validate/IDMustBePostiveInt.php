<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/3
 * Time: 18:01
 */

namespace app\api\validate;



class IDMustBePostiveInt extends BaseValidate
{
    protected $rule=[
      'id'=>'require|isPositiveInteger'
    ];
    protected $message=[
      'id'=>'id必须是正整数'
    ];

}