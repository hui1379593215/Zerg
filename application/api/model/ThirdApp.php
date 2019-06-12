<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/6/1
 * Time: 14:58
 */

namespace app\api\model;


class ThirdApp extends BaseModel
{
    public static function check($ac,$se){

        $app = self::where('app_id','=',$ac)
            ->where('app_secret','=',$se)
            ->find();

        return $app;
    }

}