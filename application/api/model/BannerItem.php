<?php

namespace app\api\model;

class BannerItem extends BaseModel
{
    //隐藏属性
    protected $hidden=['update_time','delete_time','img_id','id','banner_id'];
    //外链图片
    public function img(){
        //这是一对一的关系
        return $this->belongsTo('Image','img_id','id');
    }
}
