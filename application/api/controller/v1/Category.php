<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 12:57
 */

namespace app\api\controller\v1;
use app\api\model\Category as CategoryModel;
/*
 * @分类接口
 */
class Category
{
    protected $beforeActionList = [
        'checkPrimaryScope'=>['only'=>'getAllCategories']
    ];

    protected function checkPrimaryScope(){
        TokenServer::needPrimaryScope();


    }

    public function getAllCategories(){
        $categories=CategoryModel::all([],'img');
            //$categories->isEmpty这个是错误的之后再来学习一下
        if(empty($categories)){
            throw new CategoryException();
        }
        return $categories;
    }
}