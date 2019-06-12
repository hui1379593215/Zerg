<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/15
 * Time: 21:20
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    //自动生成时间
    protected $autoWriteTimestamp = true;


    //设置【读取器】返回一个json格式
    public function getSnapItemsAttr($value)
    {

        if (empty($value)) {
            return null;
        }

        return json_decode($value);
    }

    public function getSnapAddressAttr($value)
    {

        if (empty($value)) {
            return null;
        }

        return json_decode($value);
    }


    //根据时间倒序排列
    public static function getSummaryByUser($uid, $page = 1, $size = 15)
    {
        $paginData = self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);

        return $paginData;
    }


    //cms根据分页获取所有的订单
    public static function getSummaryByPage($page = 1, $size = 20)
    {
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData;
    }
}