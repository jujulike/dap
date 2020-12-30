<?php
// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Controller;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
use think\Db;

/**
 * 后台首页控制器
 */
class Admin extends Controller {
    public function __construct(){
        /* 读取数据库中的配置 */
        $config = cache('db_config_data');
        if(!$config){
            $config =   api('Config/lists');
            $config['var_module'] = request()->module();
            $config['var_controller'] = request()->controller();
            $config['var_action'] = request()->action();
            //$config['template']['view_path'] = APP_PATH.'admin/view/'.$config['admin_view_path'].'/'; //模板主题
			//$config['dispatch_error_tmpl' ]    =  APP_PATH .'admin'. DS .'view' . DS .$config['admin_view_path'].DS. 'public' . DS . 'error.html'; // 默认错误跳转对应的模板文件
            //$config['dispatch_success_tmpl' ]  =  APP_PATH .'admin'. DS .'view' . DS .$config['admin_view_path'].DS. 'public' . DS . 'success.html'; // 默认成功跳转对应的模板文件
            cache('db_config_data', $config);
        }
        config($config);//添加配置
        parent::__construct();
    }

    /**
     * 后台控制器初始化
     */
    public function _initialize(){
        // SESSION_ID设置的提交变量,解决flash上传跨域
        $session_id=input(config('session.var_session_id'));
        if($session_id){
            session_id($session_id);
        }
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Publics/login');
        }
        // 是否是超级管理员
        define('IS_ROOT',   is_administrator());
        if(!IS_ROOT && config('admin_allow_ip')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',config('admin_allow_ip')))){
                $this->error('403:禁止访问');
            }
        }
        // 检测系统权限
        if((!IS_ROOT)&&(UID >2)){
            $access =   $this->accessControl();
            if ( false === $access ) {
                $this->error('403:禁止访问');
            }elseif(null === $access ){
                //检测访问权限
                //$rule  = strtolower($_SERVER['PATH_INFO']);
                $rule  = strtolower($this->request->module().'/'.$this->request->controller().'/'.$this->request->action());
                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问1!'.$rule);
                }else{
                    // 检测分类及内容有关的各项动态权限
                    $dynamic    =   $this->checkDynamic();
                    if( false === $dynamic ){
                        $this->error('未授权访问2!');
                    }
                }
            }
        }
        $con=\think\Db::name('config')->where('id','5')->find();
        $this->assign('con', $con);
        $this->assign('__MENU__', $this->getAdminMenus());
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    final protected function checkRule($rule, $type=AuthRule::rule_url, $mode='url'){
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \com\Auth();
        }
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *
     * Author: zfc000000
     */
    protected function checkDynamic(){}


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * Author: zfc000000
     */
    final protected function accessControl(){
        $allow = config('allow_visit');
        $deny  = config('deny_visit');
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false; //非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null; //需要检测节点权限
    }
    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     * @param string  $method  操作方法
     * @param string $model 模型名称,供M函数使用的参数
     * 
     * * @author
     */
    //批量修改删除
    public function changeSent($method=null,$model=null,$type="status"){
        $data=input('id/a');
        $id = array_unique($data);
        $id = is_array($id) ? implode(',',$id) : $id;
        if (empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $map['id'] =   array('in',$id);
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid($model, $map,$type);
                break;
            case 'resumeuser':
                $this->resume($model, $map,$type);
                break;
            case 'deleteuser': 
                $this->delete($model, $map );
                break;
            case 'deletedata': 
                $this->deleteids($model, $map );
                break;
            default:
                $this->error("参数非法");
        }
    }
    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author
     */
    final protected function editRow ( $model ,$data, $where , $msg=false ){
        $id=input('id/a');
        if(!empty($id)){
            $id    = array_unique($id);
            $id    = is_array($id) ? implode(',',$id) : $id;
            //如存在id字段，则加入该条件
            $fields = db()->getTableFields(array('table'=>config('database.prefix').strtolower($model)));

            if(in_array('id',$fields) && !empty($id)){
                $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
            }
        }

        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>var_export(Request()->isAjax(), true)) , (array)$msg );

        if( \think\Db::name($model)->where($where)->update($data)!==false ) {
			action_log('del_hotel_order',$model,$id,UID);//记录行为
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的 where()方法的参数
     * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author
     */
    protected function forbid ( $model , $where = array() ,$type, $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
        $data    =  array($type => 0);
        $this->editRow( $model , $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author
     */
    protected function resume (  $model , $where = array() ,$type , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
        $data    =  array($type => 1);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * Author: zfc000000
     */
    protected function restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
        $data    = array('status' => 1);
        $where   = array_merge(array('status' => -1),$where);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author
     */
    protected function delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
        $data['is_delete']  = 1;
        $this->editRow(   $model , $data, $where, $msg);
    }
    /**
     * 条目删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * * @author
     */
    protected function deleteids ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
        if( \think\Db::name($model)->where($where)->delete()!==false ) {
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
        
    }
    /* @param string $model 模型名称,供D函数使用的参数*/
    //状态修改
    public function setproperty($model) {
        $id = input('id');
        $type = input('type');
        $data = input('data');;
        if ($type) {
            $data = ($data==1?'0':'1');
            \think\Db::name($model)->where(array("id"=>$id))->update(array($type=>$data));
            exit(json_encode(array("result" => 1, "data" => $data)));
        }
        die(json_encode(array("result" => 0)));
    }
    /**
     * 设置一条或者多条数据的状态
     * $Model 模型名称
     */
    public function setStatus($Model=false){
        if(empty($Model)){
            $Model=request()->controller();
        }
        $ids    =   input('ids/a');
        $status =   input('status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }
        $map['id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            case -2  :
                if(\think\Db::name($Model)->where($map)->delete()){
                    $this->success('删除成功');
                }else {
                    $this->error('删除失败！');
                }
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /*
     * 获取以及栏目
     */
    public function getmypidcate(){
        $map['is_delete']=0;
        $map['pid']=0;
        $cate=Db::name('category')->where($map)->select();
        return $cate;

    }
    /**
     * 获取后台分类菜单
     */
    final public function getAdminMenus(){
        $model_name = $this->request->module();
        // 获取主菜单
        $where['hide']  =   0;
        if(!config('develop_mode')){ // 是否开发者模式
            $where['is_dev']    =   0;
        }

        $menus_list = array();
        $menus  =   \think\Db::name('menu')->where($where)->order('sort asc,id asc')->field('id,pid,title,url,icon')->select();


        foreach ($menus as $key=>$val){
            if($val['pid'] == 0){
                $menus_list[$key]=$val;

                $menus_list[$key]['child']=array();

                foreach ($menus as $k=>$v){
                    if($v['pid'] == $val['id']){
                        $menus_list[$key]['child'][]=$v;
                        //  $child=$v;
                        //   $menus_list[$key]['child']=$child;
                    }
                }
            }
        }
        $rule  = strtolower($_SERVER['PATH_INFO']);
        //$rule  = strtolower($this->request->module().'/'.$this->request->controller().'/'.$this->request->action());
        foreach ($menus_list as $key => $item) {
            // 判断主菜单权限
            if ( !IS_ROOT && !$this->checkRule(strtolower($model_name.'/'.$item['url']),AuthRule::rule_main,null) ) {
                unset($menus_list[$key]);
                continue;//继续循环
            }
            if($rule  == url($item['url'])){
                $menus_list[$key]['class']='active';
            }

            if(isset($item['child'])){
                foreach ($item['child'] as $k => $v) {
                    if ( !IS_ROOT && !$this->checkRule(strtolower($model_name.'/'.$v['url']),AuthRule::rule_url,null) ) {
                        unset($menus_list[$key]['child'][$k]);
                        continue;//继续循环
                    }
                    if($rule  == url($v['url'])){
                        $menus_list[$key]['class']='active';
                        $menus_list[$key]['child'][$k]['class']='active';
                    }
                }
            }
        }

        // var_dump($menus_list);
        // die();
        return $menus_list;

    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * Author: zfc000000
     */
    final public function getMenus(){
        $model_name = $this->request->module();
        $controller      = $this->request->controller();
        $action_name = $this->request->action();
        session('admin_menu_list.'.$controller,null);
        $menus  =   session('admin_menu_list.'.$controller);
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!config('develop_mode')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   \think\Db::name('Menu')->where($where)->order('sort asc')->field('id,title,url')->select();
            $menus['child'] =   array(); //设置子节点

            foreach ($menus['main'] as $key => $item) {
                // 判断主菜单权限
                if ( !IS_ROOT && !$this->checkRule(strtolower($model_name.'/'.$item['url']),AuthRule::rule_main,null) ) {
                    unset($menus['main'][$key]);
                    continue;//继续循环
                }
                if(strtolower($controller.'/'.$action_name)  == strtolower($item['url'])){
                    $menus['main'][$key]['class']='current';
                }
            }

            // 查找当前子菜单
            $pid = \think\Db::name('Menu')->where("pid !=0 AND url like '%{$controller}/".$action_name."%'")->value('pid');
            if($pid){
                // 查找当前主菜单
                $nav =  \think\Db::name('Menu')->find($pid);
                if($nav['pid']){
                    $nav    =   \think\Db::name('Menu')->find($nav['pid']);
                }
                foreach ($menus['main'] as $key => $item) {
                    // 获取当前主菜单的子菜单项
                    if($item['id'] == $nav['id']){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = \think\Db::name('Menu')->where(array('group'=>array('neq',''),'pid' =>$item['id']))->distinct(true)->column("group");

                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!config('develop_mode')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = \think\Db::name('Menu')->where($where)->column('id,url');

                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,$model_name)!==0 ){
                                    $rule = $model_name.'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, AuthRule::rule_url,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid']     =   $item['id'];
                            $map['hide']    =   0;
                            if(!config('develop_mode')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = \think\Db::name('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                    }
                }
            }
            session('admin_menu_list.'.$controller,$menus);
        }
        return $menus;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * Author: zfc000000
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        $module_name = $this->request->module();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            $list = \think\Db::name('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],$module_name)!==0 ){
                    $list[$key]['url'] = $module_name.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = \think\Db::name('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],$module_name)!==0 ){
                    $nodes[$key]['url'] = $module_name.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }


    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * Author: zfc000000
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)input('request.');
        if(is_string($model)){
            $model  =   \think\Db::name($model);
        }
        $pk         =   $model->getPk();

        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            //$where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }


        $total =$model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = config('list_rows') > 0 ? config('list_rows') : 10;
        }
        // 分页查询
        $list = $model->where($options['where'])->order($order)->field($field)->paginate($listRows,false,['query'=>request()->param()]);

        // 获取分页显示
        $page = $list->render();
        // 模板变量赋值
        // $this->assign('list', $list);
        $this->assign('_page', $page);
        $this->assign('_total',$total);
        if($list && !is_array($list)){
            $list=$list->toArray();
        }
        return $list['data'];
    }

    /**
     * 处理文档列表显示
     * @param array $list 列表数据
     * @param integer $model_id 模型id
     */
    protected function parseDocumentList($list,$model_id=null){
        $model_id = $model_id ? $model_id : 1;
        $attrList = get_model_attribute($model_id,false,'id,name,type,extra');
        // 对列表数据进行显示处理
        if(is_array($list)){
            foreach ($list as $k=>$data){
                foreach($data as $key=>$val){
                    if(isset($attrList[$key])){
                        $extra      =   $attrList[$key]['extra'];
                        $type       =   $attrList[$key]['type'];
                        if('select'== $type || 'checkbox' == $type || 'radio' == $type || 'bool' == $type) {
                            // 枚举/多选/单选/布尔型
                            $options    =   parse_field_attr($extra);
                            if($options && array_key_exists($val,$options)) {
                                $data[$key]    =   $options[$val];
                            }
                        }elseif('date'==$type){ // 日期型
                            $data[$key]    =   date('Y-m-d',$val);
                        }elseif('datetime' == $type){ // 时间型
                            $data[$key]    =   date('Y-m-d H:i',$val);
                        }
                    }
                }
                $data['model_id'] = $model_id;
                $list[$k]   =   $data;
            }
        }
        return $list;
    }
    /*
     * 一键清空缓存
     */
    public function delcache() {
        $path=ROOT_PATH.'/runtime';
        $ret = '删除成功';
        $files = $this->getFiles($path);
        if (!is_array($files)) {
            $ret = $files;
        } elseif (empty($files)) {
            $ret = '删除失败,目录下没有文件或目录';
        } else {
            foreach ($files as $item => $file) {
                if (is_dir($file)) {
                    rmdir($file);
                } elseif (is_file($file)) {
                    unlink($file);
                }
            }
        }
        if($ret == '删除成功'){
            $this->success($ret);
        }else{
            $this->error($ret);
        }
    }
    //获取目录下的所有文件和目录
    //使用$path = 'a/x/s/';
    public function getFiles($path)
    {
        if (is_dir($path)) {
            $path = dirname($path) . '/' . basename($path) . '/';
            $file = dir($path);
            while (false !== ($entry = $file->read())) {
                if ($entry !== '.' && $entry !== '..') {
                    $cur = $path . $entry;
                    if (is_dir($cur)) {
                        $subPath = $cur . '/';
                        $this->getFiles($subPath);
                    }
                    $this->files[] = $cur;
                }
            }
            $file->close();
            return $this->files;
        } else {
            $this->error = $path . 'not a dir';
            return $this->error;
        }
    }
    public function upfile($arr){

        $file = request()->file($arr);

        $info = $file->move(ROOT_PATH. '/public/upload/images');
        if($info){
            $dd=$info->getSaveName();
            $t=str_replace('\\','/',$dd);
            return $t;

        }else{

            echo $file->getError();
        }
    }

    /*全部分类信息*/
    public function getCategory($model,$id){
        $model_name=$model;
        if(is_string($model)){
            $model  =   \think\Db::name($model);
        }
        $map['parent_id']=$id;
$map['is_virtual']=0;
        //循环两层即可了 最高3级分类
        $data = $model->field('cat_id,cat_name,parent_id,parent_str')->where($map)->select();
        foreach($data as $key => $val){
            $data[$key]['cate']=$this->getCategory($model_name,$val['cat_id']);
        }
        return $data;
    }

    public function getUp($ids,$parent_id){
        $model=\think\Db::name('Category');
        $iddata['cat_id']=$ids;
        $idinfo = $model->where($iddata)->find();

        if($parent_id==0){
            $data_s['parent_str']='0,';
        }else {
            $sjmap['cat_id']=$parent_id;
            $dqinfo = $model->where($sjmap)->find();
            $data_s['parent_str'] = $dqinfo['parent_str'].$dqinfo['cat_id'].',';
        }

        $idParent_str['parent_str']=$idinfo['parent_str'].$ids.',';

        $map_up['parent_str']=$data_s['parent_str'].$ids.',';
        $list_=$model->where($idParent_str)->select();
        foreach ($list_ as $kye=>$val){
            $map_wh['cat_id']=$val['cat_id'];
            $map_wh['parent_id']=$ids;
            $this->getUp($val['cat_id'],$val['parent_id']);
            $model->where($map_wh)->update($map_up);
        }
    }
//获取地址信息
	/*public function getLatLng($address=''){
		$result=$this->query_address($address);
		if(!empty($result['data'][0])){
				$address = $result['data'][0];
				// var_dump($result) ;
				sleep(0.5);
			   //print_r($address);
			   $result['lat'] = $address['location']['lat'];
			   $result['lng'] = $address['location']['lng'];
			  return $result;

		}else{
			return null;
		}
	}*/
	//获取地址信息
	public function getLatLng($address=''){
		$result=$this->query_address($address);
		if(!empty($result['data'][0])){
				$address = $result['data'][0];
				foreach($result['data'] as $v){
				if(strpos($v['district'],'江津区') !==false){
					
					$result['lat']=$v['location']['lat'];
					$result['lng']=$v['location']['lng'];
					break;
				}
			
				}
				// var_dump($result) ;
				sleep(0.5);
			   //print_r($address);
			   //$result['lat'] = $address['location']['lat'];
			   //$result['lng'] = $address['location']['lng'];
			   
			   
			  return $result;

		}else{
			return null;
		}
	}
	//地址信息
	public function query_address($key_words){
        $header[] = 'Referer: http://lbs.qq.com/webservice_v1/guide-suggestion.html';
        $header[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36';
        $url ="http://apis.map.qq.com/ws/place/v1/suggestion/?&region=&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77&keyword=".$key_words; 
 
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
 
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
         // print_r($output);die;
        //释放curl句柄
        curl_close($ch);
        // return $output;
        $result = json_decode($output,true);
         // print_r($result);
        // $res = $result['data'][0];
        return $result;
         //echo json_encode(['error_code'=>'SUCCESS','reason'=>'查询成功','result'=>$result);
	  }
}
