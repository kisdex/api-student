<?php
 
 
namespace app\api\service;
use app\api\model\FaMother;
use app\api\model\User;
use think\exception\ValidateException;
use plang\CommonService;

class UserService extends CommonService {


	/*
 	* @Description  列表数据
 	*/
	public static function indexList($where,$field,$orderby,$limit,$page){
		try{
			$res = User::where($where)->with(['other','follow','quota'])
                ->order($orderby)
                ->paginate(['list_rows'=>$limit,'page'=>$page])
                ->each(function ($key){
                    $key->father = $key->mother = null;
                    if($key->father_id){
                        $key->father = FaMother::getOne($key->father_id);
                    }
                    if($key->mother_id){
                        $key->mother = FaMother::getOne($key->mother_id);
                    }
                    unset($key->father_id,$key->mother_id);
                });
		}catch(\Exception $e){
			abort(config('my.error_log_code'),$e->getMessage());
		}
		return ['list'=>$res->items(),'count'=>$res->total()];
	}


	/*
 	* @Description  添加
 	*/
	public static function add($data){
		try{
			validate(\app\api\validate\User::class)->scene('add')->check($data);
			$data['reg_time'] = time();
            if(!empty($data['idcard'])){
                $data['age'] = self::get_age($data['idcard']);
            }
			$res = User::create($data);
		}catch(ValidateException $e){
			throw new ValidateException ($e->getError());
		}catch(\Exception $e){
			abort(config('my.error_log_code'),$e->getMessage());
		}
		return $res->id;
	}

    public static function get_age($idcard){
        if(empty($idcard)) return null;
        $date = strtotime(substr($idcard,6,8));
        $today = strtotime('today');
        $diff = floor(($today-$date)/86400/365);
        $age = strtotime(substr($idcard,6,8).' +'.$diff.'years')>$today?($diff+1):$diff;
        return $age;
    }


	/*
 	* @Description  修改
 	*/
	public static function update($where,$data){
		try{
			validate(\app\api\validate\User::class)->scene('update')->check($data);
            if(!empty($data['idcard'])){
                $data['age'] = self::get_age($data['idcard']);
            }
			$res = User::where($where)->update($data);
		}catch(ValidateException $e){
			throw new ValidateException ($e->getError());
		}catch(\Exception $e){
			abort(config('my.error_log_code'),$e->getMessage());
		}
		return $res;
	}



}

