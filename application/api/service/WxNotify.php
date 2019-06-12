<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/5/1
 * Time: 21:42
 */

namespace app\api\service;

use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if($data['result_code']=='SUCCESS'){
            //订单号
            $orderNo = $data['put_trade_no'];
            //加了事务放置前端多次传进行多次操作
            //可能涉及到高并发
            //防止对库存进行多次扣除
            Db::startTrans();
            try{

                $order = OrderModel::where('order_no','=',$orderNo)
                        ->lock(true)//加锁进行防范，不能替代事务
                        ->find();

                //判断是否有被支付过
                if($order->status == OrderStatusEnum::UNPAID){

                    //检测商品的库存
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    //查看pass是不是为true来判断【库存量】是否通过
                    if($stockStatus['pass']){

                        //更新订单的状态
                        //1.第一个是订单的ID
                        //2.是订单的库存是否通过
                        $this->updateOrderStatus($order->id,true);
                        //把商品的库存减掉
                        $this->reduceStock($stockStatus);

                    }else{
                        $this->updateOrderStatus($order->id,false);

                    }

                }
                Db::commit();
                return true;

            }catch (Exception $ex)
            {
                Log::error($ex);
                return false;
            }
        }
        else{

            //支付失败时返回true,告诉微信服务器不用在发送请求了
            return true;
        }
    }
    //更新库存
    private function reduceStock($stockStatus){
        foreach ($stockStatus['pStatusArray'] as $singlePStatus){
            //数量是$singlePStatus['count]
            //setDec用TP5模型对数据库自减操作

            Product::where('id','=',$stockStatus['id'])
                ->setDec('stock',$singlePStatus['count']);
        }
    }


    //更新订单的状态
    //1.传的是订单号
    //2.传的是是否支付成功，后是否有库存
    private function updateOrderStatus($orderID,$success){
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)
            ->update(['status'=>$status]);
    }

}