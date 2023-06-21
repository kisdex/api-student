<?php
namespace app\api\controller;


use app\api\model\Quota;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderQuota extends Common {

    function update()
    {
        $data = $this->request->post();
        try{
            if(!$data['quota_id']){
                throw new ValidateException('参数错误');
            }
            $res = Quota::update($data);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function add(){
        $postField = 'user_id,quota_text,quota_summary,quota_result';
        $data = $this->request->only(explode(',',$postField),'post',null);
        try{
            if(!$data['user_id']){
                throw new ValidateException('参数错误');
            }
            $data['create_time'] = time();
            $res = Quota::create($data);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }

        return $this->ajaxReturn($this->successCode,'操作成功');
    }

    function delete(){
        $idx =  $this->request->delete('id', '', 'serach_in');
        try{
            if(!$idx){
                throw new ValidateException("参数错误");
            }
            Quota::destroy(['quota_id'=>explode(',',$idx)]);
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return json(['status'=>$this->successCode,'msg'=>'操作成功']);
    }

    function list(){
        $limit  = $this->request->post('limit', 20, 'intval');
        $page   = $this->request->post('page', 1, 'intval');

        $order = $this->request->post('order', '', 'serach_in');    //排序字段 bootstrap-table 传入
        $sort = $this->request->post('sort', '', 'serach_in');        //排序方式 desc 或 asc
        $orderby = ($sort && $order) ? 'q.'.$sort . ' ' . $order : 'quota_id desc';
        try {
            $where['user_id'] = $this->request->post('id','','serach_in');
            if(!$where['user_id']){
                throw new ValidateException("参数错误");
            }
            $res = Db::table('stu_quota')
                ->alias('a')
                ->leftJoin('stu_user u','a.user_id = u.id')
                ->field('a.*,u.name,u.tel')
                ->where(formatWhere($where))
                ->order($orderby)
                ->paginate(['list_rows' => $limit, 'page' => $page]);

        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',['rows' => $res->items(), 'total' => $res->total()]);
    }

    function view(){
        $data['quota_id'] = $this->request->get('id','','serach_in');
        try{
            if(!$data['quota_id']){
                throw new ValidateException("参数错误");
            }
            $res  = checkData(Quota::where($data)->find());
        }catch(\Exception $e){
            abort(config('my.error_log_code'),$e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',$res);
    }
}