<?php


namespace listen;

class DoLog
{
    public function handle($event){
        if(request()->isAjax() && !in_array(request()->action(),['index','getExtends'])){
			$content = request()->except(['s', '_pjax']);
			if ($content) {
				foreach ($content as $k => $v) {
					if (is_string($v) && strlen($v) > 200 || stripos($k, 'password') !== false) {
						unset($content[$k]);
					}
				}
			}
		
			$data['application_name'] = app('http')->getName();
			$data['username'] = session(app('http')->getName().'.username');
			$data['url'] = request()->url(true);
			$data['ip'] = request()->ip();
			$data['useragent'] = request()->server('HTTP_USER_AGENT');
			$data['content'] = json_encode($content);
			$data['create_time'] = time();
			$data['type'] = 2;
			
			db('log')->insertGetId($data);
		}   
    }
}