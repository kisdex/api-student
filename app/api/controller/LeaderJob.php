<?php
namespace app\api\controller;

use app\api\model\Job;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderJob extends Common
{

    function update()
    {
        $data = $this->request->post();
        try {
            if (!$data['job_id']) {
                throw new ValidateException('参数错误');
            }
            if($data['job_time']){
                $data['job_time'] = strtotime($data['job_time']);
            }
            if($data['graducate_time']){
                $data['graducate_time'] = strtotime($data['graducate_time']);
            }
            $res = Job::update($data);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function add()
    {
        $postField = 'user_id,company,job,salary,pics,videos,job_time,graducate_time';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        try {
            if (!$data['user_id']) {
                throw new ValidateException('参数错误');
            }
            if($data['job_time']){
                $data['job_time'] = strtotime($data['job_time']);
            }
            if($data['graducate_time']){
                $data['graducate_time'] = strtotime($data['graducate_time']);
            }
            $data['leader_id'] = intval($this->request->uid);
            $data['create_time'] = time();
            $res = Job::create($data);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }

        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function delete()
    {
        $idx = $this->request->delete('id', '', 'serach_in');
        try {
            if (!$idx) {
                throw new ValidateException("参数错误");
            }
            Job::destroy(['job_id' => explode(',', $idx)]);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return json(['status' => $this->successCode, 'msg' => '操作成功']);
    }

    function list()
    {
        $limit = $this->request->post('limit', 20, 'intval');
        $page = $this->request->post('page', 1, 'intval');

        $order = $this->request->post('order', '', 'serach_in');    //排序字段 bootstrap-table 传入
        $sort = $this->request->post('sort', '', 'serach_in');        //排序方式 desc 或 asc
        $orderby = ($sort && $order) ? 'a.' . $order . ' ' . $sort : 'job_id desc';
        try {
            $where['user_id'] = $this->request->post('id','','serach_in');
            if(!$where['user_id']){
                throw new ValidateException("参数错误");
            }
            $res = Db::table('stu_job')
                ->alias('a')
                ->leftJoin('stu_user u', 'a.user_id = u.id')
                ->field('a.*,u.name,u.tel')
                ->where(formatWhere($where))
                ->order($orderby)
                ->paginate(['list_rows' => $limit, 'page' => $page]);

        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', ['rows' => $res->items(), 'total' => $res->total()]);
    }

    function view()
    {
        $data['job_id'] = $this->request->get('id', '', 'serach_in');
        try {
            if (!$data['job_id']) {
                throw new ValidateException("参数错误");
            }
            $res = checkData(Job::where($data)->find());
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', $res);
    }
}