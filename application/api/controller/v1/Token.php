<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/6
 * Time: 14:46
 */

namespace app\api\controller\v1;


use app\api\service\AppToken;
use app\api\service\UserToken;
use app\api\validate\AppTokenGet;
use app\api\validate\TokenGet;
use app\lib\exception\ParameterException;
use app\api\service\Token as TokenService;

class Token
{

    public function getToken($code=''){
        (new TokenGet())->goCheck();

        $nt = new UserToken($code);
        $token=$nt->get();
        return [
            'token' =>$token
        ];
    }

    //cms第三方应用获取token令牌
    /*
     * @url /app_token?
     * @POST ac=：ac se = :secret
     * ac 账号  se密码
     */
    public function getAppToken($ac='',$se=''){

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET');

        (new AppTokenGet())->goCheck();


        //业务层
        $app = new AppToken();
        $token = $app->get($ac,$se);

        return[
            'token'=>$token
        ];
    }




//    检测缓存中的令牌是否失效
    public function verifyToken($token = ''){
        if(!$token){
            throw new ParameterException([
               'token不允许为空'
            ]);
        }

        $valid = TokenService::verifyToken($token);
        return[
          'isValid'=>$valid
        ];
    }
}
