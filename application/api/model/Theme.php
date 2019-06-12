<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/18
 * Time: 21:21
 */

namespace app\api\model;


class Theme extends BaseModel
{
    protected $hidden=['delete_time','update_time','topic_img_id','head_img_id'];
    public function topicImg()
    {
        return $this->belongsTo('Image','topic_img_id','id');

    }
    public function headImg()
    {
        return $this->belongsTo('Image','head_img_id','id');
    }
    //多对多
    public function products()
    {
        return $this->belongsToMany('product','theme_product','product_id','theme_id');
    }

    public static function getThemeWithProducts($id)
    {
        $theme=self::with('products,topicImg,headImg')
        ->find($id);
        return $theme;
    }

}