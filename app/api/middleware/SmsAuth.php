<?php


namespace app\api\middleware;
use think\exception\ValidateException;

class SmsAuth
{
	
	//短信验证
    public function handle($request, \Closure $next)
    {	
		$verify_id = $request->param('verify_id','','strip_tags,trim');	//验证ID
		$verify	= $request->param('verify','','strip_tags,trim');	//验证码
		$mobile	= $request->param('mobile','','strip_tags,trim');	//验证手机号
		if(empty($verify_id) || empty($verify)){
			throw new ValidateException('短信验证ID或者验证码不能为空');
		}
		$cacheData = cache($verify_id);
		if($cacheData['code'] <> $verify){
			throw new ValidateException('验证码错误或者已过期');
		}
		
		if($cacheData['mobile'] <> $mobile){
			throw new ValidateException('手机号与验证不一致');
		}
		
		return $next($request);
    }
} 