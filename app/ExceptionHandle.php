<?php

 
namespace app;

use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\FuncNotFoundException;
use think\exception\ClassNotFoundException;
use think\exception\ErrorException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\template\exception\TemplateNotFoundException;
use think\Response;
use Throwable;
use think\facade\Log;
use RuntimeException;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
	
	
	private $error_log_db = true;	//异常日志是否写入数据库
	
    /**
     * Render an exception into an HTTP response.
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
		//方法不存在
        if ($e instanceof FuncNotFoundException) {
			if($request->isAjax()){
				return json(['status'=>404,'msg'=>$e->getFunc().'方法不存在']);
			}else{
				return response($e->getFunc().'方法不存在', 404);
			}
        }
		
		//控制器不存在
        if ($e instanceof ClassNotFoundException) {
			if($request->isAjax()){
				return json(['status'=>404,'msg'=>$e->getClass().'控制器不存在']);
			}else{
				return response($e->getClass().'控制器不存在', 404);
			}  
        }
		
		//模板不存在
        if ($e instanceof TemplateNotFoundException) {
			return response($e->getTemplate().'模板不存在', 404);
        }
		
		//验证器异常
        if ($e instanceof ValidateException) {
            return json(['status'=>411,'msg'=>$e->getError()]);
        }
		
		//error系统层面错误异常
        if ($e instanceof ErrorException) {
			return response($e->getMessage(), 500);
        }
		
        // 请求异常 多为自定义的请求异常 
        if ($e instanceof HttpException) {
			Log::error('错误信息:'.print_r($e->getMessage(),true));
			if($e->getStatusCode() == config('my.error_log_code') && $this->error_log_db){
				event('ExceptionLog', $e->getMessage());
			}
			return json(['status'=>$e->getStatusCode(),'msg'=>$e->getMessage()]); 
        }
		
        return parent::render($request, $e);
    }
	
	
	
}
