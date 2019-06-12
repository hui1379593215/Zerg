<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/6
 * Time: 22:22
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = ['product_id','delete_time','id'];
}