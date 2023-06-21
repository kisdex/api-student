<?php
// +----------------------------------------------------------------------
// | 应用公共文件
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 
// +----------------------------------------------------------------------


use think\facade\Db; 
use think\facade\Log; 

error_reporting(0);

function deldir($dir) {
//先删除目录下的文件：
   $dh=opendir($dir);
   while ($file=readdir($dh)) {
	  if($file!="." && $file!="..") {
		 $fullpath=$dir."/".$file;
		 if(!is_dir($fullpath)) {
			unlink($fullpath);
		 } else {
			deldir($fullpath);
		 }
	  }
   }
 
   closedir($dh);
   //删除当前文件夹：
   if(rmdir($dir)) {
	  return true;
   } else {
	  return false;
   }
}


/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}


//通过键值返回键名
function getKeyByVal($array,$data){
	foreach($array as $key=>$val){
		if($val == $data){
			$data = $key;
		}
	}
	return $data;
}


/*格式化列表*/
function formartList($fieldConfig,$list)
{
	$cat = new \org\Category($fieldConfig);
	$ret=$cat->getTree($list);
	return $ret;
}


function htmlOutList($list,$err_status=false){
	foreach($list as $key=>$row) {
		$res[$key] = checkData($row,$err_status);	
	}
	return $res;
}

//err_status  没有数据是否抛出异常 true 是 false 否
function checkData($data,$err_status=true){
	if(empty($data) && $err_status){
		abort(412,'没有数据');
	}
	
	if(is_object($data)){
		$data = $data->toArray();
	}
	
	foreach($data as $k=>$v){
		if($v && is_array($v)){
			$data[$k] = checkData($v);
		}else{
			$data[$k] = html_out($v);
		}
	}
	return $data;
	
}

//html代码输入
function html_in($str){
    $str=htmlspecialchars($str);
	$str=strip_tags($str);
    if(!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }

   return $str;
}


//html代码输出
function html_out($str){
	if(is_string($str)){
		if(function_exists('htmlspecialchars_decode')){
			$str=htmlspecialchars_decode($str);
		}else{
			$str=html_entity_decode($str);
		}
		$str = stripslashes($str);
	}
    return $str;
}


//上传文件黑名单过滤
function upload_replace($str){
	$farr = ["/php|php3|php4|php5|phtml|pht|/is"];
	$str = preg_replace($farr,'',$str);
	return $str;
}

//查询方法过滤
function serach_in($str){
	$farr = ["/^select[\s]+|insert[\s]+|and[\s]+|or[\s]+|create[\s]+|update[\s]+|delete[\s]+|alter[\s]+|count[\s]+|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i"];
	$str = preg_replace($farr,'',html_in($str));
	return trim($str);
}


/**
 * 过滤掉空的数组
 * @access protected
 * @param  array        $data     数据
 * @return array
 */
function filterEmptyArray($data = []){
	foreach( $data as $k=>$v){   
		if( !$v && $v !== 0)   
			unset( $data[$k] );   
	}
	return $data;
}

//通过字段值获取字段配置的名称
function getFieldVal($val,$fieldConfig){
	if($fieldConfig){
		foreach(explode(',',$fieldConfig) as $k=>$v){
			$tempstr = explode('|',$v);
			foreach(explode(',',$val) as $m=>$n){
				if($tempstr[1] == $n){
					$fieldvals .= $tempstr[0].',';
				}
			}
			
		}
		return rtrim($fieldvals,',');
	}
}

/**
 * tp官方数组查询方法废弃，数组转化为现有支持的查询方法
 * @param array $data 原始查询条件
 * @return array
 */
function formatWhere($data){
	$where = [];
	foreach( $data as $k=>$v){
		if(is_array($v)){
			if((!is_array($v[1]) && (string) $v[1] <> null ) || (is_array($v[1]) && (string) $v[1][0] <> null)){
				switch(strtolower($v[0])){			
					//模糊查询
					case 'like':
						$v[1] = '%'.$v[1].'%';
					break;
					
					//表达式查询
					case 'exp':
						$v[1] = Db::raw($v[1]);
					break;
				}
				$where[] = [$k,$v[0],$v[1]];

			}
		}else{
			if((string) $v != null){
				$where[] = [$k,'=',$v];
			}
		}
	}
	return $where;
}

//导出excel表头设置
function getTag($key3,$no=100){
	$data=[];
	$key = ord("A");//A--65
	$key2 = ord("@");//@--64	
	for($n=1;$n<=$no;$n++){
		if($key>ord("Z")){
			$key2 += 1;
			$key = ord("A");
			$data[$n] = chr($key2).chr($key);//超过26个字母时才会启用  
		}else{
			if($key2>=ord("A")){
				$data[$n] = chr($key2).chr($key);//超过26个字母时才会启用  
			}else{
				$data[$n] = chr($key);
			}
		}
		$key += 1;
	}
	return $data[$key3];
}


function getListUrl($newslist){
	if(!empty($newslist['jumpurl'])){
		$url =  $newslist['jumpurl'];
	}else{
		$url_type = config('xhadmin.url_type') ? config('xhadmin.url_type') : 1;
		if($url_type == 1){
			$url =  url('index/View/index',['content_id'=>$newslist['content_id']]);
		}else{
			$info = db('content')->alias('a')->join('catagory b','a.class_id=b.class_id')->where(['a.content_id'=>$newslist['content_id']])->field('a.content_id,b.filepath')->find();
			$url = $info['filepath'].'/'.$info['content_id'].'.html';
		}
		
	}
	return $url;
}

//返回图片缩略后 或水印后不覆盖情况下的图片路径
function getSpic($newslist){
	if($newslist){
		$targetimages = pathinfo($newslist['pic']);
		$newpath = $targetimages['dirname'].'/'.'s_'.$targetimages['basename'];
		return $newpath;
	}
}

function U($classid){
	$url_type = config('xhadmin.url_type') ? config('xhadmin.url_type') : 1;
	if($url_type == 1){
		$url = url('index/About/index',['class_id'=>$classid]);
	}else{
		$info = db('catagory')->where('class_id',$classid)->find();
		
		$filepath = $info['filepath'] == '/' ? '' : '/'.trim($info['filepath'],'/');
		$filename = $info['filename'] == 'index.html' ? '' : $info['filename'];
		$url = $filepath.'/'.$filename;
	}
	return $url;
}

function killword($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	if(function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif(function_exists('iconv_substr')) {
		$slice = iconv_substr($str,$start,$length,$charset);
	}else{
		$re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("",array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice.'...' : $slice;
}
	
function killhtml($str, $length=0){
	if(is_array($str)){
		foreach($str as $k => $v) $data[$k] = killhtml($v, $length);
			 return $data;
	}

	if(!empty($length)){
		$estr = htmlspecialchars( preg_replace('/(&[a-zA-Z]{2,5};)|(\s)/','',strip_tags(str_replace('[CHPAGE]','',$str))) );
		if($length<0) return $estr;
		return killword($estr,0,$length);
	}
	return htmlspecialchars( trim(strip_tags($str)) );
}



/**
 * 实例化数据库类
 * @param string        $name 操作的数据表名称（不含前缀）
 * @param array|string  $config 数据库配置参数
 * @param bool          $force 是否强制重新连接
 * @return \think\db\Query
 */
if (!function_exists('db')) {
    function db($name = '')
    {
        return Db::connect('mysql',false)->name($name);
    }
}

function isMobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
        return true;
    }
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp',
            'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu',
            'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave',
            'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

function tpl_form_field_image($name, $value = '', $default = '', $options = array()) {
    if (empty($default)) {
        $default = '/static/img/global/nopic.jpg';
    }
    $val = $default;
    if (!empty($value)) {
        $val = $value;
    }
    if (!empty($options['global'])) {
        $options['global'] = true;
    } else {
        $options['global'] = false;
    }
    if (empty($options['class_extra'])) {
        $options['class_extra'] = '';
    }
    if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
        if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
            exit('图片上传目录错误,只能指定最多两级目录,如: "url","url/d1"');
        }
    }
    $options['direct'] = true;
    $options['multiple'] = false;
    if (isset($options['thumb'])) {
        $options['thumb'] = !empty($options['thumb']);
    }
    $options['fileSizeLimit'] = intval(config('file_size')) * 1024;
    $s = '';
    if (!defined('TPL_INIT_IMAGE')) {
        $s = '
		<script type="text/javascript">
			function showImageDialog(elm, opts, options) {
				require(["util"], function(util){
					var btn = $(elm);
					var ipt = btn.parent().prev();
					var val = ipt.val();
					var img = ipt.parent().next().children();
					options = '.str_replace('"', '\'', json_encode($options)).';
					util.image(val, function(url){
						if(url.url){
							if(img.length > 0){
								img.get(0).src = url.url;
							}
							ipt.val(url.attachment);
							ipt.attr("filename",url.filename);
							ipt.attr("url",url.url);
						}
						if(url.media_id){
							if(img.length > 0){
								img.get(0).src = "";
							}
							ipt.val(url.media_id);
						}
					}, options);
				});
			}
			function deleteImage(elm){
				$(elm).prev().attr("src", "/static/img/global/nopic.jpg");
				$(elm).parent().prev().find("input").val("");
			}
		</script>';
        define('TPL_INIT_IMAGE', true);
    }

    $s .= '
		<div class="input-group ' . $options['class_extra'] . '">
			<input type="text" name="' . $name . '" value="' . $value . '"' . ($options['extras']['text'] ? $options['extras']['text'] : '') . ' class="form-control" autocomplete="off">
			<span class="input-group-btn">
				<button class="btn btn-default" type="button" onclick="showImageDialog(this);">选择图片</button>
			</span>
		</div>
		<div class="input-group ' . $options['class_extra'] . '" style="margin-top:.5em;">
			<img src="' . $val . '" onerror="this.src=\'' . $default . '\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail" ' . ($options['extras']['image'] ? $options['extras']['image'] : '') . ' width="150" />
			<em class="close" style="position:absolute; top: 0px; right: -14px;" title="删除这张图片" onclick="deleteImage(this)">×</em>
		</div>';
    return $s;
}


function tpl_form_field_multi_image($name, $value = array(), $options = array()) {
    $options['multiple'] = true;
    $options['direct'] = false;
    $options['fileSizeLimit'] = intval(config('file_size')) * 1024;
    if (isset($options['dest_dir']) && !empty($options['dest_dir'])) {
        if (!preg_match('/^\w+([\/]\w+)?$/i', $options['dest_dir'])) {
            exit('图片上传目录错误,只能指定最多两级目录,如: "url","url/d1"');
        }
    }
    $s = '';
    if (!defined('TPL_INIT_MULTI_IMAGE')) {
        $s = '
<script type="text/javascript">
	function uploadMultiImage(elm) {
		var name = $(elm).next().val();
		util.image( "", function(urls){
			$.each(urls, function(idx, url){
				$(elm).parent().parent().next().append(\'<div class="multi-item"><img onerror="this.src=\\\'./resource/images/nopic.jpg\\\'; this.title=\\\'图片未找到.\\\'" src="\'+url.url+\'" class="img-responsive img-thumbnail"><input type="hidden" name="\'+name+\'[]" value="\'+url.attachment+\'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>\');
			});
		}, ' . json_encode($options) . ');
	}
	function deleteMultiImage(elm){
		$(elm).parent().remove();
	}
</script>';
        define('TPL_INIT_MULTI_IMAGE', true);
    }

    $s .= <<<EOF
<div class="input-group">
	<input type="text" class="form-control" readonly="readonly" value="" placeholder="批量上传图片" autocomplete="off">
	<span class="input-group-btn">
		<button class="btn btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{$name}" />
	</span>
</div>
<div class="input-group multi-img-details">
EOF;
    if (is_array($value) && count($value) > 0) {
        foreach ($value as $row) {
            $s .= '
<div class="multi-item">
	<img src="' . tomedia($row) . '" onerror="this.src=\'./resource/images/nopic.jpg\'; this.title=\'图片未找到.\'" class="img-responsive img-thumbnail">
	<input type="hidden" name="' . $name . '[]" value="' . $row . '" >
	<em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
</div>';
        }
    }
    $s .= '</div>';

    return $s;
}

function file_random_name($dir, $ext) {
    do {
        $filename = random(30) . '.' . $ext;
    } while (file_exists($dir . $filename));

    return $filename;
}


function file_delete($file) {
    if (empty($file)) {
        return FALSE;
    }
    if (file_exists($file)) {
        @unlink($file);
    }
    if (file_exists(ATTACHMENT_ROOT . '/' . $file)) {
        @unlink(ATTACHMENT_ROOT . '/' . $file);
    }
    return TRUE;
}


