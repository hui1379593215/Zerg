<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/11
 * Time: 22:35
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{

    protected $rule = [
        'products'=>'checkProducts'
    ];
    //子元素的验证规则
    protected $singRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' =>'require|isPositiveInteger'
    ];
    protected function checkProducts($values){
        if(!is_array($values)){
            throw new ParameterException([
                'msg'=>'商品参数错误'
            ]);
        }
        if(empty($values)){
            throw new ParameterException([
                'msg'=>'商品列表不为空'
            ]);
        }
        foreach ($values as $value){

            $this->checkProduct($value);
        }
        return true;
    }
    protected function checkProduct($value){

        $validate = new BaseValidate($this->singRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParameterException([
               'msg'=>'商品列表参数错误'
            ]);
        }
    }

}