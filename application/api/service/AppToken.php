<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/6/1
 * Time: 14:15
 */

namespace app\api\service;


use app\api\model\ThirdApp;
use app\lib\exception\TokenException;

class AppToken extends Token
{

    public function get($ac, $se)
    {

        $app = ThirdApp::check($ac, $se);

        if (!$app) {
            throw new TokenException([
                'msg' => '授权失败',
                'errCode' => '10004'
            ]);

        } else {

            //取到数据库scope的权限32
            $scope = $app->scope;
            $uid = $app->id;

            //用数组进行包裹
            $values = [
                'scope' => $scope,
                'uid' => $uid
            ];

            $token = $this->saveToCache($values);

            return $token;

        }

    }

    private function saveToCache($values)
    {
        $token = self::generateToken();
        $expire_in = config('setting.token_expire_in');
        $result = cache($token, json_encode($values), $expire_in);
        if (!$result) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $token;
    }
}