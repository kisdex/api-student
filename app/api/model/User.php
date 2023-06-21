<?php

namespace app\api\model;
use think\Model;

class User extends Model {


	protected $pk = 'id';

 	protected $name = 'user';

    public function other(){
        return $this->hasMany(Relation::class,'user_id','id');
    }

    public function follow(){
        return $this->hasMany(Follow::class,'user_id','id');
    }

    public function quota(){
        return $this->hasMany(Quota::class,'user_id','id');
    }

    public function study(){
        return $this->hasOne(Study::class,'user_id','id')->bind([
            'pic','content',
        ]);
    }

}

