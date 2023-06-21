<?php
namespace app\api\controller;

use app\api\model\Live;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderLive extends Common
{

    function update()
    {
        $data = $this->request->post();
        try {
            if (!$data['live_id']) {
                throw new ValidateException('参数错误');
            }
            if($data['en_time']){
                $data['en_time'] = strtotime($data['en_time']);
            }
            $res = Live::update($data);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function add()
    {
        $postField = 'user_id,study_content,teacher_master,teacher_slaver,class_name,president,room,en_time';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        try {
            if (!$data['user_id']) {
                throw new ValidateException('参数错误');
            }
            if($data['en_time']){
                $data['en_time'] = strtotime($data['en_time']);
            }
            $data['leader_id'] = intval($this->request->uid);
            $res = Live::create($data);
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
            Live::destroy(['live_id' => explode(',', $idx)]);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return json(['status' => $this->successCode, 'msg' => '操作成功']);
    }

    function list()
    {
        $limit = $this->request->post('limit', 20, 'intval');
        $page = $this->request->post('page', 1, 'intval');

        try {
            $where['user_id'] = $this->request->post('id','','serach_in');
            if(!$where['user_id']){
                throw new ValidateException("参数错误");
            }
            $res = Db::table('stu_live')
                ->alias('a')
                ->leftJoin('stu_user u', 'a.user_id = u.id')
                ->field('a.*,u.name,u.tel')
                ->where(formatWhere($where))
                ->order('live_id desc')
                ->paginate(['list_rows' => $limit, 'page' => $page]);

        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', ['rows' => $res->items(), 'total' => $res->total()]);
    }

    function view()
    {
        $data['live_id'] = $this->request->get('id', '', 'serach_in');
        try {
            if (!$data['live_id']) {
                throw new ValidateException("参数错误");
            }
            $res = checkData(Live::where($data)->find());
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', $res);
    }
}