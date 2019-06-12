<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/21
 * Time: 23:21
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Config;
use think\Exception;
use think\Loader;
use think\Log;

/*
 * 1.子文件名
 * 2.指定extend的目录
 * 3.文件后缀
 */
Loader::import('WxPay.WxPay',EXTEND_PATH,'.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID)
    {
        if (!$orderID)
        {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    //主方法
    public function Pay(){
        //订单号可能根本不存在
        //订单号确实存在，但是，订单号和当前用户不匹配
        //订单号有可能已经被使用过了
        //检测库存量

        //要是检测不通过会抛出异常，就不会执行下面的操作了
        $this->checkOrderValid();
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);

        //根据返回的pass来判断检测的库存量是否通过
        if(!$status['pass']){

            return $status;
        }

        return $this->makeWxPreOrder($status['orderPrice']);

    }

    //微信预支付订单
    public function makeWxPreOrder($totalPrice){
        //需要知道openid
        $openid = Token::getCurrentTokenVar('openid');

        if(!$openid){
            throw new TokenException();
        }
        //统一下单接口
        $wxOrderData = new \WxPayUnifiedOrder();
        //1.订单号
        //2.交易类型
        //3.交易的金额(微信默认是以分作为单位的)
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice*100);
        $wxOrderData->SetBody('杂货工坊');
        $wxOrderData->SetOpenid($openid);
        //返回微信支付后的接口
        $wxOrderData->SetNotify_url(\config('secure.pay_back_url'));

        return $this->getPaySignature($wxOrderData);

    }

    //接收一个回调函数
    private function getPaySignature($orderData){
        $wxOrder = \WxPayApi::unifiedOrder($orderData);

        // 失败时不会返回result_code
        if($wxOrder['return_code'] != 'SUCCESS'
            || $wxOrder['result_code'] !='SUCCESS'){

            //把错误信息记录到日志里
            Log::record($wxOrder,'error');
            Log::record('获取预支付订单失败','error');
            //throw new Exception('获取预支付订单失败');
        }
        //模板消息prepay_id
//        $wxOrder['prepay_id']='1111';
        $this->recorPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    //生成签名的方法
    private function sign($wxOrder){
        //调用微信自己的方法来生成签名
        //1.需要appid
        //2.需要获取当前的时间
        //3.随机的字符串
        //4.统一下单返回的prepay_id
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());

        //  生成随机字符串
        $rand = md5(time() . mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
//        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        //  获取签名
        $sing = $jsApiPayData->MakeSign();

        //返回原始数据
        $rawValues = $jsApiPayData->GetValues();

        //把签名加载到原始数据里面
        $rawValues['PaySign'] = $sing;

        unset($rawValues['appId']); //把appId删除掉返回客户端也是没用的
        return $rawValues;
    }

    // 保存一个向用户发送的模板消息
    private function recorPreOrder($wxOrder){
        //对order模型进行更新操作
        OrderModel::where('id','=',$this->orderID)
            ->update(['prepay_id'=>$wxOrder['prepay_id']]);
    }

    //查询订单号
    private function checkOrderValid(){
        $order = OrderModel::where('id','=',$this->orderID)
                ->find();

        if(!$order){
            throw new OrderException();
        }

        if(!Token::isValidOperate($order->user_id))
        {
            throw new TokenException(
                [
                    'msg' => '订单与用户不匹配',
                    'errorCode' => 10003
                ]);
        }
        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg'=>'订单已经只支付过了',
                'errorCode'=>80003,
                'code'=>400
            ]);
        }

        $this->orderNO = $order->order_no;

        return true;

    }

}