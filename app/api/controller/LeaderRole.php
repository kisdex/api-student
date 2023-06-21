<?php
namespace app\api\controller;


use app\api\model\Quota;
use app\api\model\Role;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderRole extends Common {

    function update()
    {
        $postField = 'role_id,pid,name,status,description';
        $data = $this->request->only(explode(',',$postField),'post',null);
        try{
            if(!$data['role_id']){
                throw new ValidateException('参数错误');
            }
            $res = Role::update($data,['role_id'=>$data['role_id']]);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function add(){
        $postField = 'name,pid,status,description';
        $data = $this->request->only(explode(',',$postField),'post',null);
        try{
            $res = Role::create($data);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }

        return $this->ajaxReturn($this->successCode,'操作成功');
    }

    function delete(){
        $idx =  $this->request->delete('id', '', 'intval');
        try{
            if(!$idx){
                throw new ValidateException("参数错误");
            }
            Role::where('role_id',$idx)->delete();
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return json(['status'=>$this->successCode,'msg'=>'操作成功']);
    }

    function list(){
        $limit  = $this->request->post('limit', 40, 'intval');
        $offset = $this->request->post('offset', 0, 'intval');
        $page   = floor($offset / $limit) +1 ;
        try {

        $res = Role::paginate(['list_rows' => $limit, 'page' => $page]);
        if(!empty($res['rows'])){
            $res['rows'] = formartList(['role_id', 'pid', 'name','name'],$res['rows']);
        }

        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',['rows' => $res->items(), 'total' => $res->total()]);
    }

    function view(){
        $data['role_id'] = $this->request->get('id','','serach_in');
        try{
            if(!$data['role_id']){
                throw new ValidateException("参数错误");
            }
            $res  = checkData(Role::where($data)->find());
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',$res);
    }
}