<?php
// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------

namespace app\admin\Controller;
use think\Controller;
use think\Db;
use org\Verify;

/**
 * 后台首页控制器
 */
class Publics extends Controller {
    public function __construct(){
        /* 读取数据库中的配置 */
        $config = cache('db_config_data');
        if(!$config){
            $config =   api('Config/lists');
            $config ['var_module'] = request()->module();
            $config ['var_controller'] = request()->controller();
            $config ['var_action'] = request()->action();
            //$config ['template']['view_path'] = APP_PATH.'admin/view/'.$config['admin_view_path'].'/'; //模板主题
            //$config['dispatch_error_tmpl' ]    =  APP_PATH .'admin'. DS .'view' . DS .$config['admin_view_path'].DS. 'public' . DS . 'error.html'; // 默认错误跳转对应的模板文件
            //$config['dispatch_success_tmpl' ]  =  APP_PATH .'admin'. DS .'view' . DS .$config['admin_view_path'].DS. 'public' . DS . 'success.html'; // 默认成功跳转对应的模板文件
            cache('db_config_data', $config);
        }
        config($config);//添加配置
        parent::__construct();
    }

    /**
     * 后台用户登录
     */
    public function login($username = null, $password = null, $verify = null){
        if(request()->isPost()){
            /* 检测验证码 TODO: */
            /*
            if(!captcha_check($verify)){
                $this->error('验证码输入错误！');
            }
            */
            $code=$_POST['code'];
            $verify = new Verify();

            if (!$code) {
                $this->error('请输入验证码');
                //return json(['code' => -4, 'url' => '', 'msg' => '请输入验证码']);
            }
            if (!$verify->check($code)) {
                $this->error('验证码错误');
                //return json(['code' => -4, 'url' => '', 'msg' => '验证码错误']);
            }

			
            /* 调用UC登录接口登录 */
            $User = model(UcenterMember);
            $uid = $User->login($username, $password);

            if(0 < $uid){ //UC登录成功
                /* 登录用户 */
                if($User->info($uid)){ //登录用户
                    //TODO:跳转到登录前页面
                    $this->redirect('Index/index');
                    //$this->success('登录成功！', url('Index/index'));
                } else {
                    $this->error($Member->getError());
                }

            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }

                $this->error($error);
            }
        } else {
			
            if(is_login()){
                $this->redirect('Index/index');
            }else{

                /* 读取数据库中的配置 */
                $config = cache('db_config_data');
                if (!$config) {
                    $config = model('Config')->lists();
                    cache('db_config_data', $config);
                }
                config($config);//添加配置
                $con = Db::name('config')->where('id',5)->find();
                $this->assign('con', $con);
                return $this->fetch();
            }
        }
    }

    /* 退出登录 */
    public function logout(){
        if(is_login()){
            model('UcenterMember')->logout();
            session('[destroy]');
            $this->success('退出成功！', url('login'));
        } else {
            $this->redirect('login');
        }
    }
    public function checkVerify()
    {
        $verify = new Verify();
        $verify->imageH = 32;
        $verify->imageW = 100;
        $verify->codeSet = '0123456789';
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontSize = 14;
        return $verify->entry();
    }
}
