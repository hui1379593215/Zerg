<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/14
 * Time: 18:16
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;


class Order
{
    //订单商品列表，也就是客户端传递过来的products参数
    protected $oProducts;

    //真实的商品信息（包括库存量）
    protected  $products;

    protected $uid;

    public function place($uid,$oProducts){
        $this->oProducts= $oProducts;

        $this->uid = $uid;

        $this->products=$this->getProductsByOrder($oProducts);

        $status =$this->getOrderStatus();
        if(!$status['pass']){
            $status['order_id'] = -1;

            return $status;
        }

        //开始创建订单,用驼峰命令法
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;

        return $order;

    }



    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
//            ->update(['status' => OrderStatusEnum::DELIVERED]);
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order, $jumpPage);
    }


    //创建快照信息把他写入数据库

    private function createOrder($snap){
        Db::startTrans();
        try{
            $orderNo = $this->makeOrderNo();

            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;  //订单号
            $order->total_price = $snap['orderPrice'];//总价格
            $order->total_count = $snap['totalCount'];//总个数
            $order->snap_img = $snap['snapImg'];   //产品的主图
            $order->snap_name = $snap['snapName'];  //产品的主名称
            $order->snap_address = $snap['snapAddress'];   //产品的地址
            $order->snap_items = json_encode($snap['pStatus']);  //产品的基本信息
            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;
            foreach ($this->oProducts as &$p){
                $p['order_id'] = $orderID;
            }

            $orderproduct = new OrderProduct();
            $orderproduct->saveAll($this->oProducts);

            Db::commit();
            return [
                'order_no'=> $orderNo,
                'order_id'=> $orderID,
                'create_time'=>$create_time
            ];

        }catch (\Exception $ex){
            Db::rollback();
            throw new $ex;
        }

    }

    //生成订单号
    public static function makeOrderNo()
    {
        /*
         * 1.第一个是取当前的年份-2019
         * 2.取月份让后将十进制转换为16进制，然后在转换为大写的字符串
         * 3.第三个是日
         * 4.是Unicode的时间挫
         * 5.
         *
         *
         */
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }

    //生成订单快照
        private function snapOrder($status){
            /*
             * snap的快照信息
             *
             * orderPrice:订单的总价格
             * totalCount:订单的总数量
             * pStatus:商品状态
             * snapAddress:快照信息
             * snapName:订单缩略名字
             * snapImg:订单首页图片
             *
             */
            $snap = [
                'orderPrice' => 0,
                'totalCount' =>0,
                'pStatus'=>[],
                'snapAddress'=> null,
                'snapName'=>'',
                'snapImg' =>''
            ];
            $snap['orderPrice'] = $status['orderPrice'];
            $snap['totalCount'] = $status['totalCount'];
            $snap['pStatus'] = $status['pStatusArray'];
            $snap['snapAddress'] = json_encode($this->getUserAddress());
            $snap['snapName'] = $this->products[0]['name'];
            $snap['snapImg'] = $this->products[0]['main_img_url'];

            if(count($this->products)>1){

                $snap['snapName'].='等';
            }

            return $snap;

        }

     //用户的地址
    private function getUserAddress(){
        $userAddress = UserAddress::where('user_id','=',$this->uid)
            ->find();

        if(!$userAddress){
            throw new UserException([
               'msg'=>'用户收货地址不存在，下单失败',
                'errorCode'=>60001
            ]);
        }

        return $userAddress->toArray();
    }




    //提供对外的方法检测库存
    public function checkOrderStock($orderID){
        $oProducts = OrderProduct::where('order_id','=',$orderID)
        ->select();

        $this->oProducts = $oProducts;
        //查找商品数据库中真实的库存
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();

        return $status;

    }



    //客户端传递过来的参数和数据库中的进行对比检测库存
    private function getOrderStatus(){
        //1.判断是不是存在
        //2.价格
        //3.
        $status = [
            'pass' => true,
            'orderPrice'=>0,
            'totalCount'=>0,
            'pStatusArray'=>[]
        ];

        foreach ($this->oProducts as $oProduct){
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'],
                $oProduct['count'],
                $this->products
            );
            if(!$pStatus['haveStocks']){
                $status['pass'] = false;
            }
            //总价格是三个产品累加起来的
            $status['orderPrice'] +=$pStatus['totalPrice'];
            $status['totalCount'] +=$pStatus['counts'];
            array_push($status['pStatusArray'],$pStatus);

        }
        return $status;
    }

    //这里没备注，好像是订单快照
    private function getProductStatus($oPID,$oCount,$products){
        //totalPrice:单个产品的总价格
        //haveStocks：当前产品是不是有库存量
        $pIndex= -1;

        $pStatus = [
            'id'=> null,
            'haveStocks'=> false,
            'counts'=> 0,
            'price'=>0,
            'name'=>'',
            'totalPrice'=>0,
            'main_img_url'=>null
        ];

        for($i=0;$i<count($products);$i++){
            if($oPID == $products[$i]['id']){
                $pIndex= $i;
            }
        }
        if($pIndex==-1){
            //客户端传递的productid根本不存在
            throw new OrderException([
                'msg'=>'id为'.$oPID.'的商品不存在，创建商品不存在'
            ]);
        }else{
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['counts'] = $oCount;
            $pStatus['price']=$product['price'];
            $pStatus['main_img_url']=$product['main_img_url'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;

            if($product['stock'] - $oCount >=0){
                $pStatus['haveStocks']=true;
            }

            return $pStatus;

        }
    }

    //根据订单信息查找真实的商品信息
    private function getProductsByOrder($oProducts){

        $oPIDs = [];

        foreach ($oProducts as $item){
            array_push($oPIDs,$item['product_id']);
        }

        $products = Product::all($oPIDs)
            ->visible(['id','price','stock','name','main_img_url'])
            ->toArray();

        return $products;
    }
}