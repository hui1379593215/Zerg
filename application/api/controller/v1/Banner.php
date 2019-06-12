<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/3
 * Time: 15:47
 */

namespace app\api\controller\v1;


use app\api\model\Banner as BannerModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\BannerMissException;

class Banner
{
    /*
     * 获取id的banner信息
     * @url  /banner/:id
     * @http GET
     * @id banner的id号
     * banner 这里是用 find 查询的,返回的不是collection,
     * 而是模块对象, 因此用 isEmpty 方法会报错.只有 用 select
     * 查询的,才会返回 collection(数据集)
     */
    public  function getBanner($id){

        (new IDMustBePostiveInt())->goCheck();
        $banner=BannerModel::getBannerById($id);
        if(!$banner){

            throw new BannerMissException();

        }
        return json($banner);

    }
}