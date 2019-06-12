<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/30
 * Time: 18:01
 */

return[
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------

    // 小程序app_id
    'app_id'=>'wx3966baf9abd251f5',


    // 小程序app_secret要和【微信平台上一致】
    'app_secret'=>'a5e7745efb657bb234e0f8d6216f41a9',


    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
];