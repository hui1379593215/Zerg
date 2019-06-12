<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 12:58
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden=['delete_time','update_time','create_time'];
    public function img(){
        //一对一的关系
        return $this->belongsTo('Image','topic_img_id','id');
    }
}