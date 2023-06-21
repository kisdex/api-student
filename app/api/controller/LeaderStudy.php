<?php
namespace app\api\controller;

use app\api\controller\Common;
use app\api\model\Study;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderStudy extends Common
{

    function update()
    {
        $data = $this->request->post();
        try {
            if (!$data['study_id']) {
                throw new ValidateException('参数错误');
            }
            $res = Study::update($data);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function add()
    {
        $postField = 'user_id,pic,content';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        try {
            if (!$data['user_id']) {
                throw new ValidateException('参数错误');
            }
            $data['leader_id'] = $this->request->uid;
            $data['create_time'] = time();
            $res = Study::create($data);
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
            Study::destroy(['study_id' => explode(',', $idx)]);
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
        $orderby = ($sort && $order) ? 'a.' . $order . ' ' . $sort : 'study_id desc';
        try {
            $where['user_id'] = $this->request->post('id','','serach_in');
            if(!$where['user_id']){
                throw new ValidateException("参数错误");
            }
            $res = Db::table('stu_study')
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
        $data['study_id'] = $this->request->get('id', '', 'serach_in');
        try {
            if (!$data['study_id']) {
                throw new ValidateException("参数错误");
            }
            $res = checkData(Study::where($data)->find());
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', $res);
    }
}