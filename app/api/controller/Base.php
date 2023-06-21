<?php

 
namespace app\api\controller;
use think\exception\ValidateException;
use think\facade\Filesystem;
use think\facade\Validate;


class Base extends Common{
	
	
	/**
	* @api {post} /Base/upload 01、图片上传
	*/
	public function upload(){
		if(!$_FILES) throw new ValidateException('上传验证失败');
		$file = $this->request->file(array_keys($_FILES)[0]);
		$upload_config_id = $this->request->param('upload_config_id','','intval'); //上传配置id

		if(!Validate::fileExt($file,config('my.api_upload_ext')) || !Validate::fileSize($file,config('my.api_upload_max'))){
			throw new ValidateException('上传验证失败');
		}
		//检测图片路径已存在  true 检测 读取已有的图片路径 false不检测 每次都重新上传新的
		$upload_hash_status = !is_null(config('my.upload_hash_status')) ? config('my.upload_hash_status') : true;
		$fileinfo = $upload_hash_status ? db("file")->where('hash',$file->hash('md5'))->find() : false;
		if($upload_hash_status && $fileinfo && $this->checkFileExists($fileinfo['filepath'])){
			$url =  $fileinfo['filepath'];
		}else{
			$url = $this->up($file,$upload_config_id);
		}
		return json(['status'=>config('my.successCode'),'data'=>$url]);
	}
	
	protected function up($file){
		try{
			if(config('my.oss_status')){
				$url = \utils\oss\OssService::OssUpload(['tmp_name'=>$file->getPathname(),'extension'=>$file->extension()]);
			}else{
				$info = Filesystem::disk('public')->putFile(\utils\oss\OssService::setFilepath(),$file,'uniqid');
				$url = \utils\oss\OssService::getApiFileName(basename($info));
			}
		}catch(\Exception $e){
			abort(config('my.error_log_code'),$e->getMessage());
		}
		
		$upload_hash_status = !is_null(config('my.upload_hash_status')) ? config('my.upload_hash_status') : true; 
		$upload_hash_status && db('file')->insert(['filepath'=>$url,'hash'=>$file->hash('md5'),'create_time'=>time()]);
		
		return $url;
	}
	
	
	//检测文件是否存在
	public function checkFileExists($filepath){
		if(strpos($filepath, '://')){
			$res = file_get_contents($filepath) ? true: false;
		}else{
			$res = file_exists('.'.$filepath) ? true: false;
		}
		return $res;
	}
	
	
	
	/**
	* @api {get} /Base/captcha 02、图片验证码地址
	* <img src="http://xxxx.com/Base/captcha" onClick="this.src=this.src+'?'+Math.random()" alt="点击刷新验证码">
	*/
	public function captcha()
	{
		ob_clean();
	    return captcha();
	}

}

