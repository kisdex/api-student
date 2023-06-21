<?php

namespace app\api\controller;
use app\api\model\Role;
use think\App;
use think\facade\Log;
use think\exception\FuncNotFoundException;

class Common
{
    
	protected $request;
    protected $app;
	
	protected $_data;
	protected $successCode;
	protected $errorCode;
	
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
		//exit('禁止访问');
        $this->app     = $app;
        $this->request = $this->app->request;
		$this->_data = $this->request->param();
		
		//判断是否是json请求
		if(!$this->request->isJson()){
			$this->_data = $this->request->param();
		}else{
			$this->_data = json_decode(file_get_contents('php://input'),true);
		}
		
		$this->_data['timestamp'] = date('Y-m-d H:i:s', time());
		
		$this->successCode = config('my.successCode');
		$this->errorCode = config('my.errorCode');
		
		if(config('my.api_input_log')){
			Log::info('接口地址：'.request()->pathinfo().',接口输入：'.print_r($this->_data,true));
		}
//        $this->CheckAllow();
    }

    public function CheckAllow(){
        $controller = $this->request->controller();
        $action = $this->request->action();
        $app = app('http')->getName();

        $userid = $this->request->uid;
        $authList = $this->request->authList;
        $authHash = $this->request->authHash;

        if( (!$userid || $authHash == data_auth_sign($authList) )  && ( $action <> 'Login' || $action <> 'Register' )){
            abort($this->errorCode,"不允许操作");
        }

        $url =  "{$app}/{$controller}/{$action}";
        if(!in_array($url,$authList)){
            abort($this->errorCode,"你没操作权限");
        }
        return true;
    }
	
	 /**
     * 生成token
     * @param  uid 用户UID
     */
	protected function setToken($uid,$hash,$list){
		$jwt = Jwt::getInstance();
		$jwt->setIss(config('my.jwt_iss'))->setAud(config('my.jwt_aud'))->setSecrect(config('my.jwt_secrect'))->setExpTime(config('my.jwt_expire_time'));
		$token = $jwt->setUid($uid)
//            ->setAuthHash($hash)->setAuthList($list)
            ->encode()->getToken();
		return $token;
	}
	
	//接口返回
	protected function ajaxReturn($status,$msg,$data='',$token=''){
		$res = ['status'=>$status,'msg'=>$msg];
		!empty($data) && $res['data'] = $data;
		!empty($token) && $res['token'] = $token;
		return json($res);
	}
	
	public function __call($method, $args){
        throw new FuncNotFoundException('方法不存在',$method);
    }
	
}
