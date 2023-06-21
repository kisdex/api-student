<?php

namespace app\api\model;
use think\Model;

class FaMother extends Model {


	protected $pk = 'parent_id';

 	protected $name = 'parents';

    public static function getOne($id)
    {
        $ds = [];
        if(!(int)$id){
            return $ds;
        }
        $res = self::field('name,idcard,tel,job,marital_status,address,income,type,age')->where('parent_id',$id)->find();

        if(empty($res)){
            $res = [];
        }
        return $res;
    }
 

}

