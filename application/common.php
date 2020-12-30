<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件 
include ('common/common/function.php');
//if (\think\Request::instance()->isMobile()) {
//    define('VIEW_PATH', __DIR__ . '/../application/home/view/phone/');
//} else {
    define('VIEW_PATH', __DIR__ . '/../application/home/view/');
//}


/**
 * 获取请求参数
 * @param $pname 参数名
 * @param $msg 错误提示
 * @param $default 默认值，若要设置为空时取默认值，$must设置为false
 * @param $must bool 是否必须
 */
function _param($pname,$msg=null,$default='',$must=true,$is_die=true){
    $pval=input($pname);
    if($must && empty($pval)){
        if(request()->isAjax() || $is_die){//如果是ajax请求，终止，报错
            _internalError($msg);
        }
        _internalError($msg,array(),false);//否则，返回错误信息
    }
    if(!$msg){
        $must=false;
    }
    if(!$must && empty($pval)){
        $pval=$default;
    }
    return $pval;
}

/**
 * 获取当前域名
 */
function base_url(){
    return request()->domain();
}


function _internalError($msg,$code='500', $data=array(),$is_die=true) {
    header("Content-Type: application/json; charset=utf-8");
    $ret = array(
        'status'=>$code,
        'message'=>$msg
    );
    $ret['show_data'] = $data;
    if($is_die){
        die(json_encode($ret));
    }
    return $ret;
}
/**
 * ajax方式json打印正确结果或返回正确信息
 */
function _success($msg,$code='200', $data=array(),$is_die=true,$extra=array()) {
    header("Content-Type: application/json; charset=utf-8");
    $ret = array(
        'status'=>$code,
        'message'=>$msg
    );
    $ret['show_data'] = $data;
    foreach ($extra as $k => $v) {
        $ret[$k]=$v;
    }
    if($is_die){
        die(json_encode($ret));
    }
    return $ret;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string
 */
function think_user_encrypt($data, $key="", $expire = 0) {
    $key  = md5(empty($key) ? config('data_auth_key') : $key);
    //$key  = md5($key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char =  '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x=0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
    }
    return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string
 */
function think_user_decrypt($data, $key=""){
    $key  = md5(empty($key) ? config('data_auth_key') : $key);
    //$key    = md5($key);
    $x      = 0;
    $data   = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data   = substr($data, 10);
    if($expire > 0 && $expire < time()) {
        return '';
    }
    $len  = strlen($data);
    $l    = strlen($key);
    $char = $str = '';
    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char  .= substr($key, $x, 1);
        $x++;
    }
    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}
