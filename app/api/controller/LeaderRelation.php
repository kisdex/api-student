<?php
namespace app\api\controller;

use app\api\model\Job;
use app\api\model\Relation;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderRelation extends Common
{

    function update()
    {
        $data = $this->request->post();
        try {
            if (!$data['relation_id']) {
                throw new ValidateException('参数错误');
            }
            $res = Relation::update($data);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '操作成功');
    }

    function add()
    {
        $postField = 'user_id,name,relation_type,age';
        $data = $this->request->only(explode(',', $postField), 'post', null);
        try {
            if (!$data['user_id']) {
                throw new ValidateException('参数错误');
            }
            $res = Relation::create($data);
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
            Relation::destroy(['relation_id' => explode(',', $idx)]);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return json(['status' => $this->successCode, 'msg' => '操作成功']);
    }

    function view()
    {
        $data['relation_id'] = $this->request->get('id', '', 'serach_in');
        try {
            if (!$data['relation_id']) {
                throw new ValidateException("参数错误");
            }
            $res = checkData(Relation::where($data)->find());
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode, '返回成功', $res);
    }
}