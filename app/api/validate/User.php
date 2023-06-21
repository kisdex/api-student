<?php


namespace app\api\validate;
use think\validate;

class User extends validate {

	protected $rule = [
		'name'=>['require'],
		'tel'=>['require','regex'=>'/^1[345678]\d{9}$/'],
        'idcard'=>['require','regex'=>'/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/'],
		'address'=>['require'],
		'education'=>['require'],
		'graduate_school'=>['require'],
	];

	protected $message = [
		'name.require'=>'姓名不能为空',
		'tel.require'=>'手机号不能为空',
		'tel.regex'=>'手机号格式错误',
		'idcard.regex'=>'身份证格式错误',
		'address.require'=>'地址不能为空',
		'education.require'=>'学历不能为空',
		'graduate_school.require'=>'毕业院校不能为空',
	];

	protected $scene  = [
		'add'=>['name','tel','idcard','address','education','graduate_school'],
		'update'=>['name','tel','idcard','address','education','graduate_school'],
	];



}

