<?php 
define('UC_APP_ID', 1); //应用ID
define('UC_API_TYPE', 'Model'); //可选值 Model / Service
define('UC_AUTH_KEY', '!;@?rx<_2"*=S|Nmi+s.tDd-)pubI47T{BLgP1[`'); //加密KEY 
return [
	// +----------------------------------------------------------------------
	// | 模板替换
	// +----------------------------------------------------------------------
	'view_replace_str'  =>  [ 
        '__ROOT__'   => __ROOT__,
	    '__PUBLIC__' => __ROOT__.'/static2',
		'__STATIC__' => __ROOT__.'/static2/static',
		'__ADDONS__' => __ROOT__.'/static2/admin/addons',
		'__IMG__'    => __ROOT__.'/static2/admin/images',
		'__CSS__'    => __ROOT__.'/static2/admin/css',
		'__JS__'     => __ROOT__.'/static2/admin/js',
	],
    // +----------------------------------------------------------------------
    // | 会话设置
    // +---------------------------------------------------------------------- 
    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => 'session_id',
        // SESSION 前缀
        'prefix'         => 'think_admin',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],
    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => 'think_admin_',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ], 
	// +----------------------------------------------------------------------
	// | 编辑器图片上传相关配置
	// +----------------------------------------------------------------------
	'editor_upload' => array(
			'mimes'    => '', //允许上传的文件MiMe类型
			'maxSize'  => 0, //上传的文件大小限制 (0-不做限制)
			'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
			'autoSub'  => true, //自动子目录保存文件
			'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
			'rootPath' => './static/uploads/editor/', //保存根路径
			'savePath' => '', //保存路径
			'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
			'saveExt'  => '', //文件保存后缀，空则使用原后缀
			'replace'  => false, //存在同名是否覆盖
			'hash'     => true, //是否生成hash编码
			'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
	),
	// 默认跳转页面对应的模板文件
 	'dispatch_error_tmpl'     =>  APP_PATH .'admin'. DS .'view' . DS . 'public' . DS . 'error.html', // 默认错误跳转对应的模板文件
 	'dispatch_success_tmpl'   =>  APP_PATH .'admin'. DS .'view' . DS . 'public' . DS . 'success.html', // 默认成功跳转对应的模板文件
	 
];