<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/7
 * Time: 9:55
 */

namespace app\api\validate;


class AddressNew extends BaseValidate
{
    protected $rule = [
        /*
         * name:收件人的姓名
         * mobile：收件人的手机号‘
         * province,city,country省市县的地址
         * detail:具体的地址
         */
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|isMobile',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty'
    ];
}