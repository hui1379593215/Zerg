<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 17:35
 */

namespace app\api\service;



use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use app\api\model\User as UsweModel;


class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(
            config('wx.login_url'),
            $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    /**
     * @throws Exception
     * @throws WeChatException
     */
    public function get(){
        $result = curl_get($this->wxLoginUrl);

        // 注意json_decode的第一个参数true
        // 这将使字符串被转化为数组而非对象

        $wxResult = json_decode($result, true);
        //判断是不是为空
        if(empty($wxResult)){

            throw new Exception('获取session_key及openID时异常，微信内部出错');
        }else
        {
            $loginFail=array_key_exists('errcode',$wxResult);
            if($loginFail){

                $this->processLoginError($wxResult);
            }
            else{
                return $this->grantToken($wxResult);
            }
        }

    }
    private function grantToken($wxResult){
        //拿到openid
        //数据库里面看一下，这个openid是不是已经存在
        //如果存在则不处理，如果不存在则增加一条user记录
        //生成令牌返回数据，写入缓存
        //把令牌返回到客户端里去
        //key:是令牌
        //value: wxResult ,uid ,scope
        $openid = $wxResult['openid'];
        $user = UsweModel::getByOpenID($openid);
        if($user){
            //判断是不是存在openid存在的话把id传出来
            $uid=$user->id;
        }else{
            $uid=$this->newUser($openid);

        }
            $cacheValue=$this->prepareCachedValue($wxResult,$uid);
            //把数据写入缓存并生成一个key值也就是token
            $token = $this->saveTocache($cacheValue);

        return $token;
    }


    //获取key
    private function saveTocache($cacheValue){
        //调用基类的话self
        $key=self::generateToken();
        //把数组转换为字符串
        $value = json_encode($cacheValue);
        $expire_in = config('setting.token_expire_in');
        //TP5默认写入缓存的方式，默认是以文件的
        $request = cache($key,$value,$expire_in);
        if (!$request){

            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode'=>10005
            ]);

        }

        return $key;
    }



    //把数据写入缓存读出来比较快
    private function prepareCachedValue($wxResult,$uid){
        $cachedValue=$wxResult;
        $cachedValue['uid']=$uid;
        //表示权限，数字越大表示权限越大
        $cachedValue['scope']=ScopeEnum::User;
//        $cachedValue['scope']= 15 ;

        return $cachedValue;
    }
    private function newUser($openid){
        //往数据库增加一条openid并返回id
        $user=UsweModel::create([
           'openid'=>$openid
        ]);

        return $user->id;

    }
    // 处理微信登陆异常
    // 那些异常应该返回客户端，那些异常不应该返回客户端
    // 需要认真思考
    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]);
    }
}