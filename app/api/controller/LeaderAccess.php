<?php
namespace app\api\controller;


use app\api\model\Quota;
use app\api\model\Role;
use think\exception\ValidateException;
use think\facade\Db;

class LeaderAccess extends Common {

    function update(){
        $role_id = $this->request->post('role_id','','intval');
        $purval = $this->request->post('idx','','strval');
        db('access')->where('role_id',$role_id)->delete();
        foreach($purval as $val){
            $data = ['purviewval'=>$val,'role_id'=>$role_id];
            db('access')->insert($data);
        }
        return $this->ajaxReturn($this->successCode,'返回成功');
    }

    function list(){
        try {
            $role_id = $this->request->get('role_id', '', 'intval');
            $info = Role::where(['role_id'=>$role_id])->find();
            $parentInfo = Role::where(['role_id'=>$info->pid])->find();

            $parentAccess = db("access")->where('role_id', $parentInfo['role_id'])->column('purviewval', 'id');    //父角色俱备的权限
            $access = db("access")->where('role_id', $role_id)->column('purviewval', 'id');    //当前角色的权限
            $nodes = $this->getNodes(0, $access, $parentAccess, $info);
        } catch (\Exception $e) {
            abort(config('my.error_log_code'), $e->getMessage());
        }
        return $this->ajaxReturn($this->successCode,'返回成功',['nodes' => json_encode($nodes,true)]);
    }

    public function getNodes($pid,$access,$parentAccess,$info){
        $list = db("node")->where('pid',$pid)->order('sortid asc')->select()->toArray();
        if($list){
            foreach($list as $key=>$val){
                $selectStatus = false;
                $url = $val['val'];
                if(in_array($url,$access)){
                    $selectStatus = true;
                }
                if(in_array($url,$parentAccess) || empty($info['pid']) || $info['pid'] == 1){
                    $menus[$key]['text'] = $val['title'].' '.$url;
                    $menus[$key]['state'] = ['opened'=>true,'selected'=>$selectStatus];
                    $sublist = db("node")->where('pid',$val['id'])->order('sortid asc')->select()->toArray();
                    if($sublist){
                        $menus[$key]['children'] = $this->getNodes($val['id'],$access,$parentAccess,$info);
                    }
                }
            }
        }
        return array_values($menus);
    }
}