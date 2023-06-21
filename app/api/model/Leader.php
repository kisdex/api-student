<?php

namespace app\api\model;
use think\Model;

class Leader extends Model {


	protected $pk = 'leader_id';

 	protected $name = 'leader';

    public static function getAuthType($id)
    {
        $ds = null;
        if(!(int)$id){
            return $ds;
        }
        $res = self::field('department')->where('id',$id)->find();
        return $res['department'];
    }
}

