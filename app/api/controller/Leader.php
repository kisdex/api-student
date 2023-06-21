<?php

namespace app\api\controller;


use app\api\service\LeaderService;
use app\api\service\UserService;
use plang\CommonService;
use think\exception\ValidateException;
use think\facade\Log;

class Leader extends Common {

    function register(){
        $postField = 'name,tel,passwd,repasswd,role_ids';
        $data = $this->request->only(explode(',',$postField),'post',null);
        if($data['passwd'] !== $data['repasswd']){
            return $this->ajaxReturn($this->errorCode, '两次密码不一致,请检查重试');
        }
        unset($data['repasswd']);
        $data['password'] = md5($data['passwd'].config('my.password_secrect'));
        $res = LeaderService::add($data);
        return $this->ajaxReturn($this->successCode,'操作成功',$res);
    }


    function update(){
        $postField = 'leader_id,name,tel,role_ids';
        $data = $this->request->only(explode(',',$postField),'post',null);
        if(empty($data['leader_id'])){
            throw new ValidateException('参数错误');
        }
        $where['leader_id'] = $data['leader_id'];
        $res = LeaderService::update($where,$data);
        return $this->ajaxReturn($this->successCode,'操作成功');
    }

    function delete(){
        $idx =  $this->request->delete('id', '', 'serach_in');
        try{
            if(!$idx){
                throw new ValidateException("参数错误");
            }
            \app\api\model\Leader::destroy(['leader_id'=>explode(',',$idx)]);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return json(['status'=>$this->successCode,'msg'=>'操作成功']);
    }

    function list(){
        $limit  = $this->request->post('limit', 20, 'intval');
        $page   = $this->request->post('page', 1, 'intval');
        try {
            $sql = 'select a.name,a.tel,a.status,a.reg_time,group_concat(b.name) as role_name from stu_leader as a left join stu_role as b on find_in_set(b.role_id,a.role_ids) group by a.leader_id';
            $limit = ($page-1) * $limit.','.$limit;
            $res = CommonService::loadList($sql,[],$limit,'a.leader_id desc');

        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',$res);
    }

    function view(){
        $data['leader_id'] = $this->request->get('id','','serach_in');
        try{
            if(!$data['leader_id']){
                throw new ValidateException("参数错误");
            }
            $field = "name,tel,status,role_ids,reg_time";
            $res  = checkData(\app\api\model\Leader::where($data)->field($field)->find());
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',$res);
    }

    /*重置密码*/
    function uppass()
    {
        $postField = 'id,passwd,repasswd';
        $ds = $this->request->only(explode(',', $postField), 'post', null);
        try {
            if (empty($ds['id'])) throw new ValidateException("参数错误");
            if ($ds['passwd'] != $ds['repasswd']) {
                throw new ValidateException("两次密码不一致，请重新输入");
            }
            $data['password'] = md5($ds['passwd'] . config('my.password_secrect'));
            \app\api\model\Leader::update($data,['leader_id' => $ds['id']]);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功');
    }



    public function login(){
        $postField = 'mobile,passwd';
        $data = $this->request->only(explode(',', $postField), 'post', 'serach_in');

        if(!preg_match("/^1[3456789]\d{9}$/",$data['mobile'])){
            return $this->ajaxReturn($this->errorCode, '手机号格式错误');
        }
        $where['tel'] = $data['mobile'];
        $where['password'] = md5($data['passwd'].config('my.password_secrect'));
        $field = 'leader_id as id,name,tel,status,role_ids';
        $leader_info = \app\api\model\Leader::where($where)->field($field)->find();
        if(empty($leader_info)){
            return $this->ajaxReturn($this->errorCode, '用户或密码错误,请重试');
        }
        $ds = $leader_info;
        $auth_list = db("access")->where('role_id','in',$leader_info['role_ids'])->column('purviewval','id');
        $auth_list = array_unique($auth_list);

        $ds['token'] = $this->setToken($leader_info->leader_id,data_auth_sign($auth_list),$auth_list);
        $ds['exptime'] = time()+ config('my.jwt_expire_time');


        return $this->ajaxReturn($this->successCode, '登录成功',$ds);
    }



}

