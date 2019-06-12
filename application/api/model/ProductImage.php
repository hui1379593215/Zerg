<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/6
 * Time: 22:04
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden=['img_id','delete_time','product_id'];

    public function imgUrl(){

        return $this->belongsTo('Image','img_id','id');
    }

}