<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/21
 * Time: 23:00
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{

    //cms可以访问，管理员不能访问
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'getPreOrder']
    ];


    //请求预订单信息
    public function getPreOrder($id=''){
        (new IDMustBePostiveInt())->goCheck();

        $pay = new PayService($id);
        return $pay->Pay();

    }


    //做转发
    public function redirectNotift(){
        //通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒

        //1.即使支付成功也要检测库存量
        //2.更新订单的status状态
        //3.减库存
        //4.如果成功处理，我们返回微信成功处理的信息，否则，我们需要返回没有成功处理

        //特点：post xml格式：不会携带参数
        $notify = new  WxNotify();
        $notify->Handle();

    }
    //接受微信支付的回调函数
    public function receiveNotift(){
        //通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒

        //1.即使支付成功也要检测库存量
        //2.更新订单的status状态
        //3.减库存
        //4.如果成功处理，我们返回微信成功处理的信息，否则，我们需要返回没有成功处理

        //特点：post xml格式：不会携带参数
//        $notify = new  WxNotify();
//        $notify->Handle();

        $xmlData = file_get_contents('php://input');
        $result = curl_post_raw('http:/z.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133',
            $xmlData);
//        return $result;
//        Log::error($xmlData);

    }

}