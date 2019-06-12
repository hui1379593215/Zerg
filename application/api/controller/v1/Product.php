<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 11:51
 */

namespace app\api\controller\v1;
use app\api\model\Product as ProductModel;

use app\api\validate\Count;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;

class Product
{
    //客户端不传进来默认是15条
    public function getRecent($count=15){
        (new Count())->goCheck();
        $products=ProductModel::getMostRecent($count);
        if(!$products){
            throw new ProductException();
        }
        //隐藏掉summary只有在这个接口隐藏
        $products=$products->hidden(['summary']);
        return $products;
    }

    //定义分类的接口
    public function getAllInCategory($id){
        (new IDMustBePostiveInt())->goCheck();
        $products=ProductModel::getproductsBycategoryID($id);
        if($products->isEmpty()){
            throw new ProductException();
        }
        $products=$products->hidden(['summary']);
        return $products;
    }
    //商品详情
    public function getOne($id){
        (new IDMustBePostiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);

        if(!$product){
            throw new ProductException();

        }

        return $product;
    }
}