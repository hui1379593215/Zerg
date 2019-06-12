<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/7
 * Time: 12:29
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden = ['id','delete_time','user_id'];
}