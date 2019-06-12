<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/3
 * Time: 21:16
 */

namespace app\api\model;


class Banner extends BaseModel
{
    //隐藏属性
    protected $hidden=['update_time','delete_time'];
    public function items(){
        //传入三个参数：关联的表，关联的关键字，id
        //一对多的关系
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerById($id){

        $banner= (new self)->with(['items','items.img'])->find($id);

        return $banner;
    }
}