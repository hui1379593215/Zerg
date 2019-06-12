<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/3
 * Time: 17:23
 */

namespace app\api\validate;


use think\Validate;

class TestValidate extends Validate
{
     protected $rule=[
         'name'=>'require|max:10',
         'email'=>'email'
     ];
}