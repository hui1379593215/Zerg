<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 17:31
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address(){
        //没有关联关系的情况下
        return $this->hasOne('UserAddress','user_id','id');
    }


    public static function getByOpenID($openid){
        $user=self::where('openid','=',$openid)
        ->find();

        return $user;
    }

}