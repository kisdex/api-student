<?php

namespace plang;
use think\facade\Db;
use think\facade\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CommonService
{
	
	/**
	 * 生成sql查询语句
	 * @access protected
	 * @param  sql     原始sql语句
	 * @param  $where  查询条件
	 * @param  $limit  分页
	 * @param  $orderby  排序
	 * @return array
	 */
    public static function  loadList($sql,$where=[],$limit,$orderby){
		$sql = strtolower($sql);
		$map = '';
		foreach($where as $key=>$val){
			if(is_array($val)){
				switch($val[1]){
					case 'between':
						if(empty($val[2][0]) && !empty($val[2][1])){
							$map .= $val[0].' < '.$val[2][1].' and ';
						}
						if(!empty($val[2][0]) && empty($val[2][1])){
							$map .= $val[0].' > '.$val[2][1].' and ';
						}
						if(!empty($val[2][0]) && !empty($val[2][1])){
							$map .= $val[0].' between '.$val[2][0].' and '.$val[2][1].' and ';
						}
					break;
					
					case 'exp':
						$map .= $val[0].' '.$val[2].' and ';
					break;
					
					case 'in':
						$map .= $val[0].' in ('.$val[2].') and ';
					break;
					
					case 'find in set':
						$map .= ' find_in_set(\''.$val[2].'\','.$val[0].') and ';
					break;
					
					default:
						$map .= $val[0].' '.$val[1]." '".$val[2]."'".' and ';
					break;
				}	
			}
		}
		$map .= '1=1';

		$is_where = strripos($sql,"where");
		if($is_where === false){
			$where = !empty($where) ?  ' where '.$map : '';
			$sql = $sql.$where;
		}else{
			$l_sql = substr($sql, 0, $is_where);
			$r_sql = substr($sql, $is_where+5, strlen($sql)- $is_where - 5);
			$where = !empty($where) ?  ' where '.$map.' and ' : ' where ';
			$sql = $l_sql . $where . $r_sql;
		}
		
		$group = preg_split('/group\s+by/',$sql);
		if(is_array($group)){
			$is_where = strripos($group[1],"where");
			if($is_where){
				$l_sql = substr($group[1], 0, $is_where);
				$r_sql = substr($group[1], $is_where+5, strlen($group[1])- $is_where - 5);
				$where = !empty($is_where) ? 'where' : '';
				$sql = $group[0] . $where.$r_sql.' group by '.$l_sql;
			}
		}
		$limit = ' limit '.$limit;
		
		$countWhere = 'select count(*) as count from ('.$sql.') as tp';
		
		if (strripos($sql,"order by")=== false && $orderby) {
			$sql .= ' order by '.$orderby;
		}
		
		if (strripos($sql,"limit")=== false) {
			$sql .= $limit;
		}

		try{
			$result = Db::query($sql);
			$count = Db::query($countWhere);
		}catch(\Exception $e){
			log::error('sql错误：'.print_r($e->getMessage(),true));
			log::error('错误语句：'.print_r($sql,true));
		}

		return ['rows'=>$result,'total'=>$count[0]['count']];
	}
	
	//导入excel数据
	public static function importData($key){
		$file = explode('.', $_FILES['file_name']['name']);
		if (!in_array(end($file), array('xls','xlsx','csv'))) {
			throw new \Exception('请选择xls文件！');
			exit;
		}
		$path = $_FILES['file_name']['tmp_name'];
		if (empty($path)) {
			throw new \Exception('请选择要上传的文件！');
			exit;
		}

		set_time_limit(0);

		$spreadsheet = IOFactory::load($path);
		$worksheet = $spreadsheet->getActiveSheet();
		
		$sheet = $spreadsheet->getActiveSheet();
		$res = [];

		foreach ($sheet->getRowIterator(1) as $row) {
			$tmp = [];
			foreach ($row->getCellIterator() as $cell) {
				$tmp[] = $cell->getFormattedValue();
			}
			
			if(filterEmptyArray($tmp)){
				$res[$row->getRowIndex()] = $tmp;
			}
		}
		
		return $res;
	}

	
    
}
 