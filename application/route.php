<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//首页轮播图
Route::get("api/:version/banner/:id","api/:version.Banner/getBanner");
//专栏分类
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
//专栏里面的元素
Route::get('api/:version/theme/:id','api/:version.Theme/getComplexOne');
//最新的产品
Route::get('api/:version/product/recent','api/:version.Product/getRecent');
//分类里面的内容
Route::get('api/:version/product/by_category','api/:version.Product/getAllInCategory');
//商品详情对id进行规则的限定判断
Route::get('api/:version/product/:id','api/:version.Product/getOne',[],['id'=>'\d+']);
//所有分类·
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');
//根据code获取token令牌
Route::post('api/:version/token/user','api/:version.Token/getToken');

//重新获取token
Route::post('api/:version/token/verify','api/:version.Token/verifyToken');


//cms
//获取token令牌
Route::post('api/:version/token/app','api/:version.Token/getAppToken');

//收货地址
Route::post('api/:version/address','api/:version.Address/createOrUpdateAddress');

//获取用户地址
Route::get('api/:version/address','api/:version.Address/getUserAddress');

//Route::get('api/:version/address/second','api/:version.Address/second');

//下单接口
Route::post('api/:version/order','api/:version.Order/placeOrder');

//订单详情接口
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);


//历史订单接口
Route::get('api/:version/order/by_user','api/:version.Order/getSummarByUser');

//cms获取所有的订单接口
Route::get('api/:version/order/paginate', 'api/:version.Order/getSummary');

Route::put('api/:version/order/delivery', 'api/:version.Order/delivery');

//支付接口
Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');

//回调api接口
Route::post('api/:version/pay/notify','api/:version.Pay/receiveNotift');

Route::post('api/:version/pay/re_notify','api/:version.Pay/redirectNotift');



