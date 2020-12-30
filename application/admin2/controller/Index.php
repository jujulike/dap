<?php
// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------

namespace app\admin\controller;

/**
 * 后台首页控制器
 * Author: zfc000000
 */
use think\Db;

class Index extends Admin
{

    /**
     * 后台首页
     * Author: zfc000000
     */
    public function index()
    {
        $start  = strtotime(date('Y-m-d'));
        $end    = strtotime(date('Y-m-d 23:59:59'));
        $ostart = strtotime(date('Y-m-d') . ' -1 day');
        $oend   = strtotime(date('Y-m-d 23:59:59') . ' -1 day');
        //商城
        $dataset['todayorder']   = Db::name('shop_order')->whereTime('pay_time', 'today')->where('sent', '>', 0)->count(); //今日订单数量
        $dataset['yestdayorder'] = Db::name('shop_order')->whereTime('pay_time', 'yesterday')->where('sent', '>', 0)->count(); //昨日订单数量

        $dataset['todayordermoney']   = Db::name('shop_order')->whereTime('pay_time', 'today')->where('sent', '>', 0)->sum('totalprice'); //今日订单金额
        $dataset['yestdayordermoney'] = Db::name('shop_order')->whereTime('pay_time', 'yesterday')->where('sent', '>', 0)->sum('totalprice'); //昨日订单金额
       
        //用户

        $dataset['todayuser']   = Db::name('user')->whereTime('addtime', 'today')->count(); //今日新用户
        $dataset['yestdayuser'] = Db::name('user')->whereTime('addtime', 'yesterday')->count(); //昨日用户

        $this->assign('dataset', $dataset);

        $this->assign('meta_title', '管理首页');
        return $this->fetch();
    }

}
