<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/18
 * Time: 21:19
 */

namespace app\api\controller\v1;


use app\api\validate\IDCollection;
use app\api\model\Theme as ThemeModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ThemeException;

class Theme
{
    public function getSimpleList($ids="")
    {
        (new IDCollection())->goCheck();
        $ids=explode(',',$ids);
        $result=ThemeModel::with('topicImg','headImg')
        ->select($ids);
        //!$result本来是array现在是collection是对象
        if($result->isEmpty()){
            throw new ThemeException();
        }else{

        }
        return $result;
    }
    /*
     * @url /theme/:id
     */
    public function getComplexOne($id){
        //判断$id是不是整数
        (new IDMustBePostiveInt())->goCheck();
        $theme=ThemeModel::getThemeWithProducts($id);
        if(!$theme){
            throw new ThemeException();
        }
        return $theme;


    }

}