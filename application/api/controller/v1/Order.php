<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/10
 * Time: 21:42
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePostiveInt;
use app\api\validate\OrderPlace;
use app\api\service\Order as OrderServer;
use app\api\service\Token as TokenServer;
use app\api\model\Order as OrderModel;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    //用户在选择商品后，向API提交包含所有选择商品
    //API载接受到信息后，需要检查订单相关的库存量
    //有库存把订单数据存入库存中= 下单成功，返回客户端消息，告诉客户端可以支付了
    //调用我们的支付接口，进行支付
    //还需要再次进行库存量检测
    //服务端可以调用微信支付接口，进行支付
    //小程序会根据服务器返回结果拉起微信支付
    //微信会返回我们一个支付的经过（异步）
    //成功：也需要进行库存量检查
    //成功：进行库存量的检查
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getSummarByUser']
    ];


    //历史订单接口
    public function getSummarByUser($page = 1, $size = 5)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenServer::getCurrentUid();

        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty()) {

            return [
                'data' => [],
                //获取当前页
                'current_page' => $pagingOrders->currentPage()
            ];
        }
        $data = $pagingOrders
            ->hidden(['snap_items', 'snap_address', 'prepay_id'])
            ->toArray();
        return [
            'data' => $data,
            'current_page' => $pagingOrders->currentPage()
        ];

    }


    //订单详情接口
    public function getDetail($id)
    {

        (new IDMustBePostiveInt())->goCheck();

        $orderDetail = OrderModel::get($id);
        if (!$orderDetail) {
            throw new OrderException();
        }

        return $orderDetail->hidden(['prepay_id']);
    }

    public function placeOrder()
    {

        (new OrderPlace())->goCheck();
        //数组形式要加一个/a获取前台传过来的数组
        $products = input('post.products/a');
        $uid = TokenServer::getCurrentUid();

        $order = new OrderServer();

        $status = $order->place($uid, $products);

        return $status;

    }

    /**
     * 获取全部订单简要信息（分页）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummary($page=1,$size=20){

        (new PagingParameter())->goCheck();

        //根据显示的页数，和条数去数据库查找相对应的信息
        $pagingOrders = OrderModel::getSummaryByPage($page,$size);

        if($pagingOrders->isEmpty()){

            return [
                'current_page'=>$pagingOrders->currentPage(),
                'data'=>[]
            ];
        }

        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();

        return [
            'current_page'=>$pagingOrders->currentPage(),
            'data'=>$data
        ];
    }


    public function delivery($id){
        (new IDMustBePostiveInt())->goCheck();
        $order = new OrderServer();
        $success = $order->delivery($id);
        if($success){
            return new SuccessMessage();
        }
    }

}