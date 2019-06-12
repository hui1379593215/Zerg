<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 16:44
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule=[
      'code'=>'require|isNotEmpty'
    ];
    protected $message=[
        'code'=>'没有传入code值，无法获取Token'
    ];

}