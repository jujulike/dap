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
 * 行为控制器
 * Author: zfc000000.
 */
class Action extends Admin
{
    /**
     * 行为日志列表
     * Author: zfc000000.
     */
    public function actionLog()
    {
        //获取列表数据
        $map['status'] = ['gt', -1];
        $list = $this->lists('ActionLog', $map, 'id desc');
        int_to_string($list);
        foreach ($list as $key => $value) {
            $list[$key]['model_id'] = 0;
        }
        $this->assign('_list', $list);
        $this->assign('meta_title', '行为日志');

        return $this->fetch();
    }

    /**
     * 查看行为日志
     * Author: zfc000000.
     */
    public function edit($id = 0)
    {
        empty($id) && $this->error('参数错误！');

        $info = \think\Db::name('ActionLog')->field(true)->find($id);

        $this->assign('info', $info);
        $this->assign('meta_title', '查看行为日志');

        return $this->fetch();
    }

    /**
     * 删除日志.
     *
     * @param mixed $ids
     *                   Author: zfc000000
     */
    public function remove($ids = 0)
    {
        empty($ids) && $this->error('参数错误！');
        if (is_array($ids)) {
            $map['id'] = ['in', $ids];
        } elseif (is_numeric($ids)) {
            $map['id'] = $ids;
        }
        $res = \think\Db::name('ActionLog')->where($map)->delete();
        if ($res !== false) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 清空日志.
     */
    public function clear()
    {
        $res = \think\Db::name('ActionLog')->where('1=1')->delete();
        if ($res !== false) {
            $this->success('日志清空成功！');
        } else {
            $this->error('日志清空失败！');
        }
    }
}
