<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 11:55
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule=[
        //传进来的参数1到15，bettween验证1到15的数据
      'count'=>'isPositiveInteger|between:1,15'
    ];

    protected $message=[
        'count'=>'count传的必须是整数'
    ];
}