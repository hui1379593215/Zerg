<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/7
 * Time: 9:50
 */

namespace app\api\controller\v1;
use app\api\controller\BaseController;
use app\api\model\User as UserModel;
use app\api\model\UserAddress;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;
use app\api\service\Token as TokenServer;


class Address extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope'=>['only'=>'createOrUpdateAddress,getUserAddress']
    ];


    //获取收货地址
    public function getUserAddress(){
        $uid = TokenServer::getCurrentUid();
        $userAddress = UserAddress::where('user_id',$uid)
            ->find();
        if(!$userAddress){
            throw new UserException([
               'msg'=>'用户地址不存在',
                'errorCode'=>60001
            ]);
        }
        return $userAddress;
    }



    //添加收货地址
    public function createOrUpdateAddress(){
        //根据Token来获取uid
        //根据uid来查找用户数据，判断用户是否存在，如果不存在抛出异常
        //获取用户从客户端获取地址信息
        //根据地址信息判断是否存在，从而判断是否添加还是修改
        $validate = new AddressNew();
        $validate->goCheck();
        $uid = TokenServer::getCurrentUid();
        $user = UserModel::get($uid);
        if(!$user){
            throw new UserException();
        }

        //模拟用户传递的地址
        $dataArray = $validate->getDataByRule(input('post.'));
        $userAddress = $user->address;

        if(!$userAddress){

            $user->address()->save($dataArray);
        }else{
            //更新
            $user->address->save($dataArray);

        }
        return json(new SuccessMessage(),201);
    }
}