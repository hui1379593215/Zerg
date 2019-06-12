<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/18
 * Time: 21:20
 */

namespace app\api\model;


class Product extends BaseModel
{
    //pivot起到一个中间键
    protected $hidden=['delete_time','main_img_id','pivot','from','category_id','create_time','update_time'];

    //读取器的写法
    public function getMainImgUrlAttr($value,$data){

        return $this->prefixImgUrl($value,$data);
    }


    //获取【商品详情】里面的组图
    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }
    //获取【商品详情】里面的基本信息
    public function properties(){
        return $this->hasMany('ProductProperty','product_id','id');
    }


    //查询新品
    public static function getMostRecent($count){
        $product=self::limit($count)
            //排序desc降序
            ->order('create_time desc')
            ->select();

        return $product;
    }

    public static function getproductsBycategoryID($categoryID){

        $products=self::where('category_id','=',$categoryID)
        ->select();

        return $products;

    }

    public static function getProductDetail($id){
        $product = self::with([
                'imgs'=>function($query){
                    $query->with(['imgUrl'])
                        ->order('order','asc');
                }
        ])
        ->with('properties')
        ->find($id);

        return $product;
    }
}