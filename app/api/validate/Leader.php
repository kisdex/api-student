<?php

namespace app\api\validate;
use think\validate;

class Leader extends validate {


	protected $rule = [
		'name'=>['require'],
		'tel'=>['require','regex'=>'/^1[345678]\d{9}$/'],
        'role_ids'=>['require'],
	];

	protected $message = [
		'name.require'=>'姓名不能为空',
		'tel.require'=>'手机号不能为空',
		'tel.regex'=>'手机号格式错误',
        'role_ids.require'=>'所属不为为空',
	];

	protected $scene  = [
		'add'=>['name','tel','role_ids'],
		'update'=>['name','tel'],
	];



}

