<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/18
 * Time: 19:12
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    //加载图片完整路径
    protected function prefixImgUrl($value,$data){
        $finalUrl=$value;
        //用data从数据库取值
        if ($data['from'] == 1){
            $finalUrl = config('setting.img_prefix').$value;
        }else{
            return $finalUrl;
        }
        return $finalUrl;

    }

}