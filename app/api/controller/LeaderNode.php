<?php
namespace app\api\controller;


use app\api\model\Node;

class LeaderNode extends Common {
    function list(){
        $limit  = $this->request->post('limit', 20, 'intval');
        $page   = $this->request->post('page', 1, 'intval');

        try {
            $res = Node::paginate(['list_rows' => $limit, 'page' => $page]);
            $res['rows'] = formartList(['id', 'pid', 'title','title'],$res['rows']);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',['rows' => $res->items(), 'total' => $res->total()]);
    }
}