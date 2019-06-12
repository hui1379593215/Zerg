<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/4/6
 * Time: 18:41
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Cache;
use think\Exception;
use think\Request;

class Token
{
    public static function generateToken(){
        //32个字符组成的一组随机字符串
        $randChars = getRandChar(32);
        //用三组字符串进行Md5加密
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt盐
        $salt = config('secure.token_salt');

        return md5($randChars.$timestamp.$salt);
    }


    public static function getCurrentTokenVar($key){
        $token = Request::instance()
            ->header('token');

        //写入缓存
        $vars = Cache::get($token);
        if(!$vars){

            throw new TokenException();

        }else{
            //读缓存的时候有可能会是数组
            if(!is_array($vars)){
                //把对象转换为数组
                $vars = json_decode($vars,true);
            }
            //判断数组是不是存在key有才返回回去
            if(array_key_exists($key,$vars)){

                return $vars[$key];
            }else{
                throw new Exception('尝试获取的Token变量不存在');
            }

        }
    }

    public static function getCurrentUid(){
        //token
        $uid = self::getCurrentTokenVar('uid');

        return $uid;

    }

    //把前置方法提取出来ScopeEnum::User
    //需要用户和CMS管理员都可以访问的接口
    public static function needPrimaryScope(){

        $scope = self::getCurrentTokenVar('scope');
        //判断token传进来是否过期
        if($scope){

            if($scope >=ScopeEnum::User){
                return true;

            }else{
                throw new ForbiddenException();
            }

        }else{

            throw new TokenException();
        }
    }

    //只有用户才能访问权限
    public static function needExculsiveScope(){

        $scope = self::getCurrentTokenVar('scope');

        //判断token传进来是否过期
        if($scope){

            if($scope ==ScopeEnum::User){
                return true;

            }else{
                throw new ForbiddenException();
            }

        }else{

            throw new TokenException();
        }
    }

    //被检测的UID和我们用户令牌的uid是不是同一个
    public static function isValidOperate($checkedUID){

        if(!$checkedUID){

            throw new Exception('检查UID时必须传入一个被检查时的UID');
        }

        $currentOperateUID = self::getCurrentUid();

        if($currentOperateUID == $checkedUID){
            return true;
        }
        return false;
    }


    //判断一下token是不是还存在缓存中
    public static function verifyToken($token){
        $exist = Cache::get($token);
        if($exist){
            return true;
        }else{
            return false;
        }
    }
}