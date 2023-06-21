<?php

return [
	
	'upload_dir'		=> './uploads',			//文件上传根目录
	'upload_subdir'		=> 'Ym',				//文件上传二级目录 标准的日期格式
	'nocheck'			=> [],   				//不需要验证权限的url
	'img_show_status'	=> true,				//图片输入框 鼠标移动上去 是否显示图片 true 显示 false 不显示
	'error_log_code'	=> 500,					//写入日志的状态码
	
	'export_per_num'	=> 50,					//excel每次导入的数据量 建议不要高于200
	'import_type'	=> 'xls',					//可选格式有 xls、xlsx、csv 
	
	'password_secrect'	=> 'plang',			//密码加密秘钥
	
	//api基本配置
	'api_input_log'		=> true,				//api参数输入记录日志(全局)
	'successCode'		=> '200',				//成功返回码
	'errorCode'			=> '201',				//错误返回码
	'jwtExpireCode'		=> '101',				//jwt过期
	'jwtErrorCode'		=> '102',				//jwt无效
  
	//jwt鉴权配置
	'jwt_expire_time'		=> 7 * 12 * 3600,		//token过期时间 默认7天
	'jwt_secrect'			=> 'boTCfOGKwqTNKArT',	//签名秘钥
	'jwt_iss'				=> 'client.plang',	//发送端
	'jwt_aud'				=> 'server.plang',	//接收端
	
	//api上传配置
	'api_upload_domain'	=> '',						//如果做本地存储 请解析一个域名到/public/upload目录  也可以不解析
	'api_upload_ext'	=> 'jpg,png,gif,mp4',			//api允许上传文件
	'api_upload_max'	=> 200 * 1024 * 1024,			//默认2M
	
	'upload_hash_status'=>true,	//检测是否存在已上传的图片并返回原来的图片路径 true 检测 false 不检测  默认为true如果不设置
	'filed_name_status'=>false,		//true 设置字段时自动读取拼音作为字段名
	'reset_button_status'=> false,	//列表搜索重置按钮状态 true开启 false关闭 需要重新生成
	'api_upload_auth'=> true,	    //api应用上传是否验证token  true 验证 false不验证 需要重新生成

    'nocheck'=>[
        "Api/Leader/login",
        "Api/Leader/register",
        "Api/User/register",
    ],
    'allow'=>[
        1=>[
//            "Api/LeaderQuota/update",
//            "Api/LeaderQuota/add",
//            "Api/LeaderQuota/delete",
//            "Api/LeaderQuota/view",
//            "Api/LeaderQuota/list",
        ],
        2=>[

        ],
        3=>[

        ],
    ],

    'department'=>[
        'mark'=>[
            1=>"营销小组",
            2=>"宣传小组"
        ],
        'county'=>[
            1=>"元胡县",
            2=>"合开县"
        ],
        'village'=>[
            1=>"五家村",
            2=>"六加坡"
        ],
    ]
	

];
