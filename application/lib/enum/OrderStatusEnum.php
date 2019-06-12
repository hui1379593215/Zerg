<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/22
 * Time: 22:49
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    //订单状态
    //待支付
    const UNPAID = 1;

    //已支付
    const PAID = 2;

    //已发货
    const DELIVERED = 3;

    //已支付，但是库存不足
    const PAID_BUT_OUT_OF = 4;

}