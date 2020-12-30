<?php

// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------

namespace app\admin\controller;

/*
 * 后台首页控制器
 * Author: zfc000000
 */
use think\Db;

class Index extends Admin
{
    /**
     * 后台首页
     * Author: zfc000000.
     */
    public function index()
    {
        $start = strtotime(date('Y-m-d'));
        $end = strtotime(date('Y-m-d 23:59:59'));
        $ostart = strtotime(date('Y-m-d').' -1 day');
        $oend = strtotime(date('Y-m-d 23:59:59').' -1 day');
        //商城
        $dataset['todayorder'] = Db::name('shop_order')->whereTime('pay_time', 'today')->where('sent', '>', 0)->count(); //今日订单数量
        $dataset['yestdayorder'] = Db::name('shop_order')->whereTime('pay_time', 'yesterday')->where('sent', '>', 0)->count(); //昨日订单数量

        $dataset['todayordermoney'] = Db::name('shop_order')->whereTime('pay_time', 'today')->where('sent', '>', 0)->sum('totalprice'); //今日订单金额
        $dataset['yestdayordermoney'] = Db::name('shop_order')->whereTime('pay_time', 'yesterday')->where('sent', '>', 0)->sum('totalprice'); //昨日订单金额

        //用户

        $dataset['todayuser'] = Db::name('user')->whereTime('addtime', 'today')->count(); //今日新用户
        $dataset['yestdayuser'] = Db::name('user')->whereTime('addtime', 'yesterday')->count(); //昨日用户

        $this->assign('dataset', $dataset);

        $this->assign('meta_title', '管理首页');

        return $this->fetch();
    }

    public function address()
    {
        $map['id'] = ['>', '0'];
        $list = $this->lists('Sysaddress', $map, 'id desc');
        foreach ($list as $key => $val) {
            $list[$key]['area'] = array_filter(explode('|', $val['fanw']));
            $list[$key]['qsj'] = Db::name('sysadoption')->where('syaddid', $val['id'])->value('minnum');
            $list[$key]['moneys'] = Db::name('sysadoption')->where('syaddid', $val['id'])->select();
        }
        $this->assign('_list', $list);

        $this->assign('meta_title', '配送区域');

        return $this->fetch();
    }

    public function addnew()
    {
        $model = Db::name('sysaddress');
        $id = input('id');
        if (request()->isPost()) {
            $ids = $_POST['id'];
            $data = $_POST;
            $data['sorts'] = intval($data['sorts']); //排序
            if (!$data['jwd']) {
                $this->error('配送区域不能为空');
            }
            $rebate = $_POST['rebate'];

            unset($data['rebate']);

            if ($ids) {//如果存在 就是修改
                $map['id'] = ['in', $ids];
                $up = $model->where($map)->update($data);
                if ($rebate) {
                    foreach ($rebate as $v) {
                        $option_id = $v['option_id'];
                        if ($option_id) {
                            unset($v['option_id']);
                            Db::name('sysadoption')->where(['id' => $option_id])->update($v);
                        } else {
                            $v['syaddid'] = $ids;
                            Db::name('sysadoption')->insert($v);
                        }
                    }
                }
            } else {  //添加
                $data['addtime'] = time();
                $ids = $model->insertGetId($data);
                if ($ids) {
                    if ($rebate) {
                        foreach ($rebate as $v) {
                            $v['syaddid'] = $ids;
                            Db::name('sysadoption')->insert($v);
                        }
                    }
                }
            }
            // action_log('updata_good','good',$ids,UID);//记录行为
            $this->success('操作成功', 'address');
        }

        if ($id) {
            $item = $model->where('id', $id)->find();
            $item['area'] = array_filter(explode('|', $item['fanw']));

            $item['point'] = array_filter(explode('|', $item['jwd']));
            $t = [];
            foreach ($item['point'] as $k => $val) {
                $ar = explode(',', $val);
                $t[$k]['lat'] = $ar['0'];
                $t[$k]['lng'] = $ar['1'];
            }
            $item['points'] = json_encode($t);

            $option = Db::name('sysadoption')->where('syaddid', $id)->select();

            $this->assign('item', $item);
            $this->assign('option', $option);
            $this->assign('meta_title', '编辑配送区域');
        } else {
            $this->assign('meta_title', '添加配送区域');
        }

        return $this->fetch();
    }
}
