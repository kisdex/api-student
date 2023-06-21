<?php

//接口路由文件

use think\facade\Route;

Route::post('upload', 'Base/upload')->middleware(['JwtAuth']);	//文件上传;

Route::post('leader_register', '/Leader/register'); //市场人员注册
Route::post('leader_login', '/Leader/login');     //市场人员登录
//员工操作
Route::post('ldr_up', '/Leader/update')->middleware(['JwtAuth']);       //修改
Route::delete('ldr_del', '/Leader/delete')->middleware(['JwtAuth']);   //删除
Route::get('ldr_info', '/Leader/view')->middleware(['JwtAuth']);      //详情
Route::post('ldr_ls', '/Leader/list')->middleware(['JwtAuth']);      //列表
Route::post('ldr_upss', '/Leader/uppass')->middleware(['JwtAuth']);   //修改密码


Route::post('stu_register', '/LeaderUser/register');     //学生注册
Route::get('leader_view', '/LeaderUser/list')->middleware(['JwtAuth']);  //市场人员查看所有学生的简略列表
Route::get('leader_info', '/LeaderUser/view')->middleware(['JwtAuth']);       //市场人员查看所有学生的详细信息
Route::post('leader_upstatus', '/LeaderUser/updateStatus')->middleware(['JwtAuth']);  //特殊人员修改学生状态
Route::post('leader_up', '/LeaderUser/update')->middleware(['JwtAuth']);  //修改
Route::delete('leader_del', '/LeaderUser/delete')->middleware(['JwtAuth']);  //删除

//父母信息
Route::post('leader_upfamily', '/LeaderUser/updateFamily')->middleware(['JwtAuth']);  //特殊人员完善学生家庭信息
Route::post('leader_addfamily', '/LeaderUser/addFamily')->middleware(['JwtAuth']);  //特殊人员修改学生家庭信息
Route::delete('leader_delfamily', '/LeaderUser/delFamily')->middleware(['JwtAuth']);  //删除
Route::get('leader_infofamily', '/LeaderUser/viewFamily')->middleware(['JwtAuth']);  //详情

//兄妹信息
Route::post('leader_uprelation', '/LeaderRelation/update')->middleware(['JwtAuth']);  //修改
Route::post('leader_addrelation', '/LeaderRelation/add')->middleware(['JwtAuth']);  //添加
Route::delete('leader_delrelation', '/LeaderRelation/delete')->middleware(['JwtAuth']);  //删除
Route::get('leader_inforelation', '/LeaderRelation/view')->middleware(['JwtAuth']);  //详情

//学生跟踪信息
Route::post('leader_upfollow', '/LeaderFollow/update')->middleware(['JwtAuth']);    //修改
Route::post('leader_addfollow', '/LeaderFollow/add')->middleware(['JwtAuth']);      //添加
Route::delete('leader_delfollow', '/LeaderFollow/delete')->middleware(['JwtAuth']);   //删除
Route::get('leader_infofollow', '/LeaderFollow/view')->middleware(['JwtAuth']);    //详情
Route::post('leader_lsfollow', '/LeaderFollow/list')->middleware(['JwtAuth']);      //列表

//学生话术信息
Route::post('leader_upquota', '/LeaderQuota/update')->middleware(['JwtAuth']);      //修改
Route::post('leader_addquota', '/LeaderQuota/add')->middleware(['JwtAuth']);        //添加
Route::delete('leader_delquota', '/LeaderQuota/delete')->middleware(['JwtAuth']);     //删除
Route::get('leader_infoquota', '/LeaderQuota/view')->middleware(['JwtAuth']);      //详情
Route::post('leader_lsquota', '/LeaderQuota/list')->middleware(['JwtAuth']);        //列表

//学生风采信息
Route::post('leader_upjob', '/LeaderJob/update')->middleware(['JwtAuth']);      //修改
Route::post('leader_addjob', '/LeaderJob/add')->middleware(['JwtAuth']);        //添加
Route::delete('leader_deljob', '/LeaderJob/delete')->middleware(['JwtAuth']);     //删除
Route::get('leader_infojob', '/LeaderJob/view')->middleware(['JwtAuth']);      //详情
Route::post('leader_lsjob', '/LeaderJob/list')->middleware(['JwtAuth']);        //列表

//学生就业信息
Route::post('leader_upstudy', '/LeaderStudy/update')->middleware(['JwtAuth']);      //修改
Route::post('leader_addstudy', '/LeaderStudy/add')->middleware(['JwtAuth']);        //添加
Route::delete('leader_delstudy', '/LeaderStudy/delete')->middleware(['JwtAuth']);     //删除
Route::get('leader_infostudy', '/LeaderStudy/view')->middleware(['JwtAuth']);      //详情
Route::post('leader_lsstudy', '/LeaderStudy/list')->middleware(['JwtAuth']);        //列表

//学生在校信息
Route::post('leader_uplive', '/LeaderLive/update')->middleware(['JwtAuth']);      //修改
Route::post('leader_addlive', '/LeaderLive/add')->middleware(['JwtAuth']);        //添加
Route::delete('leader_dellive', '/LeaderLive/delete')->middleware(['JwtAuth']);     //删除
Route::get('leader_infolive', '/LeaderLive/view')->middleware(['JwtAuth']);      //详情
Route::post('leader_lslive', '/LeaderLive/list')->middleware(['JwtAuth']);        //列表

//系统信息
Route::post('leader_uprole', '/LeaderRole/update')->middleware(['JwtAuth']);      //修改
Route::post('leader_addrole', '/LeaderRole/add')->middleware(['JwtAuth']);        //添加
Route::delete('leader_delrole', '/LeaderRole/delete')->middleware(['JwtAuth']);     //删除
Route::get('leader_inforole', '/LeaderRole/view')->middleware(['JwtAuth']);      //详情
Route::post('leader_lsrole', '/LeaderRole/list')->middleware(['JwtAuth']);        //列表

Route::get('leader_lsacs', '/LeaderAccess/list')->middleware(['JwtAuth']);        //权限列表
Route::post('leader_upacs', '/LeaderAccess/update')->middleware(['JwtAuth']);     //设置权限

