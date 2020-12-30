<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2020/10/13
 * Time: 10:40
 */

namespace app\admin\controller;


use think\Db;

class Shop extends Admin
{
    public function _initialize()
    {
        return parent::_initialize(); // TODO: Change the autogenerated stub
    }

    /*
     * 商家列表
     */
    public function index()
    {
        $map['sid']=array('>','0');
        $list = $this->lists('Supuser', $map, 'sid desc');
        foreach ($list as $key=>$val) {
            if($val['type']){
                $type=explode(",", $val['type']);
            }
        }

        $category = Db::name('category')->where(['is_delete'=>1])->select();
        $this->assign("category",$category);
        $this->assign('_list', $list);
        $this->assign('meta_title', '商家管理');
        return $this->fetch();

    }

    public function addshop()
    {

        return $this->fetch();
    }

    public function editshop()
    {

    }
}