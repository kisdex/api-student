<?php


namespace utils\oss;


class OssService
{


    private static $filepath;	//文件存储路径

    /**
     * 设置本地存储路径
     * @param  string type 业务号
     * @return string
     */
    public static function setFilepath(){
        self::$filepath = app('http')->getName().'/'.date(config('my.upload_subdir'));
        return self::$filepath;
    }


    /**
     * 返回本地存储完整文件路径 后台的
     * @param  string type 业务号
     * @return string
     */
    public static function getAdminFileName($filename){
        if(config('phtyuche.domain')){
            $url = config('phtyuche.domain').'/'.self::$filepath.'/'.$filename;
        }else{
            $url = ltrim(config('my.upload_dir'),'.').'/'.self::$filepath.'/'.$filename;
        }
        return $url;
    }

    /**
     * 返回本地存储完整文件路径 api的
     * @param  string type 业务号
     * @return string
     */
    public static function getApiFileName($filename){
        if(config('my.api_upload_domain')){
            $url = config('my.api_upload_domain').'/'.self::$filepath.'/'.$filename;
        }else{
            $url = ltrim(config('my.upload_dir'),'.').'/'.self::$filepath.'/'.$filename;
        }
        return $url;
    }

    /**
     * 图片oss存储路径
     * @param  string type 业务号
     * @return string
     */
    public static function setKey($type,$tmpInfo){

        $array = [
            'mp4'=>['mp4','avi','wmv','mpeg','mov'],
            'mp3'=>['MP3','m4a','amv','wav','ogg'],
            'doc'=>['doc','docx','xls','xlsx','ppt','zip','rar','txt','pdf'],
            'img'=>['jpg','jpeg','png','gif','bmp']
        ];


        foreach($array as $key=>$val){
            if(in_array(strtolower($tmpInfo['extension']),$val)){
                $ext = $key;
            }
        }

        $filepath = $ext.'/'.doOrderSn($type).'.'.$tmpInfo['extension']; //上传路径
        return $filepath;
    }


    /**
     * oss开始上传
     * @param  string tmpInfo 图片临时文件信息
     * @return string oss返回图片完整路径
     */
    public static function OssUpload($tmpInfo){
        abort(config('my.error_log_code'),"请配置云服务oss");
        switch(config('my.oss_default_type')){
            case 'ali';
                //todo 请配置云服务oss
                break;
        }
        return "";
    }



}
