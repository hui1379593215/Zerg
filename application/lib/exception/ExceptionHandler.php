<?php
/**
 * Created by PhpStorm.
 * User: xiaohui
 * Date: 2019/3/8
 * Time: 22:19
 */

namespace app\lib\exception;

use think\exception\Handle;
use think\Log;
use think\Request;

//继承tp5的异常处理类
//所有的错误异常类
class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;
    //返回客户端当前调试的URl路径
    public function render(\Exception $e)
    {
        //config下的exception_handle异常处理设置当前的路径
        //判断用户是不是自定义异常
        if($e instanceof BaseException)
        {

            //如果是自定义异常
            $this->code=$e->code;
            $this->msg=$e->msg;
            $this->errorCode=$e->errorCode;

        }
        else
         {
            //后端开发人员要看到错误信息
//             Config::get('app_debug');
             if(config('app_debug')){
                //调用父类的异常处理方法，就可以还原了
                 return parent::render($e);

             }else{

                 //服务端错误
                 $this->code = 500;
                 $this->msg = '服务端错误，不想告诉你';
                 $this->errorCode= 999;
                 //记错内部错误的日志
                 $this->recordErrorLog($e);
             }

        }
        //获取前端返回的路径
        $request = Request::instance();
        $result=[
            'msg'=>$this->msg,
            'error_code'=>$this->errorCode,
            //获取错误的路径
            'request_url'=> $request->url()

        ];
        return json($result,$this->code);
    }
    private function recordErrorLog(\Exception $e){
        //开启日志错误
        Log::init([
            'type'=>'File',
            'path'=>LOG_PATH,
            'level'=>['error']
        ]);
        //第一个参数记录日志的错误信息
        //第二个参数日志的级别
        Log::record($e->getMessage(),'error');
    }

}