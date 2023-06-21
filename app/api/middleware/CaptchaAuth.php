<?php


namespace app\api\middleware;
use think\exception\ValidateException;

class CaptchaAuth
{
	
    public function handle($request, \Closure $next)
    {	
		$captcha	= $request->param('captcha','','strip_tags,trim');	//验证码
		if(empty($captcha)){
			throw new ValidateException('验证码不能为空');
		}
		
		if(!captcha_check($captcha)){
			throw new ValidateException('验证码错误');
		}
		
		return $next($request);	
    }
} 