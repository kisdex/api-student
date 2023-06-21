<?php

namespace app\api\controller;

use app\api\model\FaMother;
use app\api\model\User;
use app\api\service\UserService;
use think\exception\ValidateException;

class LeaderUser extends Common {

    function list(){
        $limit  = $this->request->get('limit', 20, 'intval');
        $page   = $this->request->get('page', 1, 'intval');

        $where = [];
        $where['name'] = $this->request->get('username', '', 'serach_in');
        $where['tel'] = $this->request->get('mobile', '', 'serach_in');

        $create_time_start = $this->request->get('create_time_start', '', 'serach_in');
        $create_time_end = $this->request->get('create_time_end', '', 'serach_in');

        $where['reg_time'] = ['between',[strtotime($create_time_start),strtotime($create_time_end)]];

        $field = 'name,tel,age,idcard,address,education,graduate_school,major,status,score_y,score_s,score_e,hobby,change_note,user_note,parents_note';
        $orderby = 'id desc';
        $res = UserService::indexList(formatWhere($where),$field,$orderby,$limit,$page);

        return $this->ajaxReturn($this->successCode,'返回成功',$res);
    }


    function register(){
        $postField = 'name,tel,idcard,address,education,graduate_school,major,score_y,score_s,score_e,hobby,change_note,user_note,parents_note';
        $data = $this->request->only(explode(',',$postField),'post',null);
        $res = UserService::add($data);
        return $this->ajaxReturn($this->successCode,'操作成功',$res);
    }

    function updateStatus(){
        $postField = 'id,status';
        $data = $this->request->only(explode(',',$postField),'post',null);

        try{
            if(!$data['id']) {
                throw new ValidateException('参数错误');
            }
            \app\api\model\User::update($data);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return json(['status'=>$this->successCode,'msg'=>'操作成功']);
    }

    function updateFamily(){
        $postField = 'parent_id,name,idcard,tel,job,marital_status,address,income,type';
        $data = $this->request->only(explode(',',$postField),'post',null);

        try{
            if ($data['parent_id']) {
                $where['parent_id'] = $data['parent_id'];
                if(!empty($data['idcard'])){
                    $data['age'] = UserService::get_age($data['idcard']);
                }

                $res = FaMother::update($data,$where);
            } else {
                throw new ValidateException('参数错误');
            }
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return json(['status'=>$this->successCode,'msg'=>'操作成功']);
    }

    function addFamily(){
        $postField = 'user_id,name,idcard,tel,job,marital_status,address,income,type';
        $data = $this->request->only(explode(',',$postField),'post',null);

        try{
            if ($data['user_id']) {
                if(!empty($data['idcard'])){
                    $data['age'] = UserService::get_age($data['idcard']);
                }
                $res = FaMother::create($data);
                if($data['type'] == 1){
                    $ds['mother_id'] = $res->mother_id;
                }else{
                    $ds['father_id'] = $res->father_id;
                }
                if(!empty($data['idcard'])){
                    $ds['age'] = UserService::get_age($data['idcard']);
                }
                $userWhere['id'] = $data['user_id'];
                \app\api\model\User::update($ds,$userWhere);
            } else {
                throw new ValidateException('参数错误');
            }
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', $res);
    }

    function view(){
        $data['id'] = $this->request->get('id', '', 'serach_in');
        try {
            if (!$data['id']) {
                throw new ValidateException("参数错误");
            }
            $res  = checkData(\app\api\model\User::where($data)
                ->with(['other','follow','quota'])
                ->find());
            if(!empty($res)){
                $res['father'] = $res['mother'] = null;
                if($res['father_id']){
                    $res['father'] = FaMother::getOne($res['father_id']);
                }
                if($res['mother_id']){
                    $res['mother'] = FaMother::getOne($res['mother_id']);
                }
                unset($res['father_id'],$res['mother_id']);
            }

        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', $res);
    }

    /*修改*/
    function update(){
        $data = $this->request->post();
        try{
            if(!$data['id']) {
                throw new ValidateException('参数错误');
            }
            \app\api\model\User::update($data);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return json(['status'=>$this->successCode,'msg'=>'操作成功']);
    }

    /*删除*/
    function delete(){
        $idx = $this->request->delete('id', '', 'serach_in');
        try {
            if (!$idx) {
                throw new ValidateException("参数错误");
            }
            User::destroy(['id' => explode(',', $idx)]);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return json(['status' => $this->successCode, 'msg' => '操作成功']);
    }



}

