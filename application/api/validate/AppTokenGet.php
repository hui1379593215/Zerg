<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/6/1
 * Time: 14:10
 */

namespace app\api\validate;


class AppTokenGet extends BaseValidate
{

    //cms账号密码登录的验证器
    protected $rule = [
        'ac' => 'require|isNotEmpty',
        'se' => 'require|isNotEmpty'

        ];

}