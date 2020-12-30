<?php
/**
 * Created by PhpStorm.
 * User: wm
 * Date: 2018/3/13
 * Time: 17:57.
 */

namespace app\admin\controller;

use think\Db;

/**
 * 行为控制器
 * Author: zfc000000.
 */
class Coupon extends Admin
{
    //活动板块
    public function catetype()
    {
        $_list = $this->lists('catetype', $map, 'sorts asc');
        $this->assign('_list', $_list);
        $this->assign('meta_title', '活动管理');

        return $this->fetch();
    }

    //限时活动
    public function hotgood()
    {
        $map['is_xs'] = ['eq', 1];

        $keyword = input('keyword');
        if ($keyword) {
            $map['title'] = ['like', '%'.(string) $keyword.'%'];
        }

        $list = $this->lists('good', $map, 'tj_sorts asc');
        foreach ($list as $key => $val) {
            $shopname = Db::name('supuser')->where('sid', $val['sid'])->value('shopname');
            $list[$key]['shopname'] = $shopname;
            if ($val['photo_x']) {
                $img = explode(',', $val['photo_x']);
                $list[$key]['photo_x'] = $img[0];
            }
            $list[$key]['addtime'] = date('Y-m-d H:i', $val['addtime']);
        }
        $this->assign('_list', $list);
        $this->assign('meta_title', '限时活动');

        return $this->fetch();
    }

    //今日特价
    public function todaygood()
    {
        $map['is_tj'] = ['eq', 1];

        $keyword = input('keyword');
        if ($keyword) {
            $map['title'] = ['like', '%'.(string) $keyword.'%'];
        }

        $list = $this->lists('good', $map, 'tj_sorts asc');
        foreach ($list as $key => $val) {
            $shopname = Db::name('supuser')->where('sid', $val['sid'])->value('shopname');
            $list[$key]['shopname'] = $shopname;
            if ($val['photo_x']) {
                $img = explode(',', $val['photo_x']);
                $list[$key]['photo_x'] = $img[0];
            }
            $list[$key]['addtime'] = date('Y-m-d H:i', $val['addtime']);
        }
        $this->assign('_list', $list);
        $this->assign('meta_title', '今日特价');

        return $this->fetch();
    }

    public function set_sorts()
    {
        $res = Db::name('catetype')->where(['id' => input('id')])->update(['sorts' => input('values')]);
    }

    public function set_hot_sotr()
    {
        $res = Db::name('Supuser')->where(['sid' => input('id')])->update(['hot_sotr' => input('values')]);
    }

    public function set_tj_sorts()
    {
        $res = Db::name('good')->where(['id' => input('id')])->update(['tj_sorts' => input('values')]);
    }

    public function set_h_price()
    {
        $res = Db::name('good')->where(['id' => input('id')])->update(['h_price' => input('values')]);
    }

    public function set_x_sorts()
    {
        $res = Db::name('good')->where(['id' => input('id')])->update(['x_sorts' => input('values')]);
    }

    public function set_x_price()
    {
        $res = Db::name('good')->where(['id' => input('id')])->update(['x_price' => input('values')]);
    }

    public function hotshop()
    {
        $map['is_hot'] = ['eq', 1];

        $keyword = input('keyword');
        if ($keyword) {
            $map['shopname'] = ['like', '%'.(string) $keyword.'%'];
        }

        $list = $this->lists('Supuser', $map, 'sid asc');
        foreach ($list as $key => $val) {
            $where['is_delete'] = ['eq', 0];
            $where['id'] = ['in', $val['type']];
            $category = Db::name('category')->where($where)->column('catename');
            $list[$key]['catename'] = implode(' || ', $category);
            $list[$key]['addtime'] = date('Y-m-d H:i', $val['addtime']);
        }
        $this->assign('_list', $list);
        $this->assign('meta_title', '热门店铺');

        return $this->fetch();
    }

    public function addcatetype()
    {
        $model = Db::name('catetype');
        $id = input('id');
        if (request()->isPost()) {
            $ids = $_POST['id'];
            $data = $_POST;
            if ($ids) {//如果存在 就是修改
                $map['id'] = ['eq', $ids];
                $up = $model->where($map)->update($data);
            } else {  //添加
                $data['addtime'] = time();
                $up = $ids = $model->insertGetId($data);
            }
            if ($up) {
                action_log('updata_catetype', 'catetype', $ids, UID); //记录行为
                $this->success('操作成功', 'catetype');
            } else {
                $this->error('操作失败');
            }
        }

        if ($id) {
            $catetype = $model->where('id', $id)->find();
            $this->assign('catetype', $catetype);
            $this->assign('meta_title', '编辑活动');
        } else {
            $this->assign('meta_title', '添加活动');
        }

        return $this->fetch();
    }

    //优惠券管理
    public function index()
    {
        $map['is_delete'] = 0;
        $_list = $this->lists('Coupon', $map, 'id desc');
        foreach ($_list as &$v) {
            $v['count'] = Db::name('mycoupon')->where(['couponid' => $v['id']])->count();
            $v['sycount'] = Db::name('mycoupon')->where(['couponid' => $v['id'], 'sent' => 1])->count();
        }
        $this->assign('_list', $_list);
        $this->assign('meta_title', '优惠券管理');

        return $this->fetch();
    }

    /*
     *
     *优惠券【添加、编辑】
     */
    public function addcoupon()
    {
        $model = Db::name('Coupon');
        $id = input('id');
        if (request()->isPost()) {
            $ids = $_POST['id'];
            $data = $_POST;
            if ($ids) {//如果存在 就是修改
                $map['id'] = ['in', $ids];
                $up = $model->where($map)->update($data);
            } else {  //添加
                $data['addtime'] = time();
                $up = $ids = $model->insertGetId($data);
            }
            if ($up) {
                action_log('updata_coupon', 'coupon', $ids, UID); //记录行为
                $this->success('操作成功', 'coupon');
            } else {
                $this->error('操作失败');
            }
        }

        if ($id) {
            $coupon = $model->where('id', $id)->find();
            $this->assign('coupon', $coupon);
            $this->assign('meta_title', '编辑优惠券');
        } else {
            $this->assign('meta_title', '添加优惠券');
        }

        return $this->fetch();
    }

    //删除优惠券
    public function delcoupon()
    {
        $did = input('id');
        $da['is_delete'] = '1';
        $res = Db::name('Coupon')->where(['id' => $did])->update($da);
        if ($res) {
            action_log('updata_coupon', 'coupon', $did, UID); //记录行为
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function zt()
    {
        $model = Db::name('info');
        if (request()->isPost()) {
            $data = $_POST;
            if ($_FILES['file']['name']) {
                $path = $this->upfile('file');
                $data['zt_pic'] = '/upload/images/'.$path;
            }
            $up = $model->where('id', 1)->update($data);
            $this->success('操作成功');
        }
        $goodid = '';
        $item = $model->where('id', 1)->field('id,zt_name,zt_pic,zt_desc')->find();

        $ztType = Db::name('shopzt')->where('type', 3)->select();
        if ($ztType) {
            $g = array_column($ztType, 'goodid');
            $goodStr = implode(',', $g);
            $goodId = $goodStr;
            foreach ($ztType as $key => $val) {
                $ztType[$key]['good'] = Db::name('good')->where('id', 'in', $val['goodid'])->select();
            }
        }

        $ztAllGood = Db::name('good')->where('id', 'in', $goodId)->select();
        $ztShop = Db::name('shopzt')->where('type', '2')->find();
        $ztShop['shop'] = Db::name('supuser')->where('sid', 'in', $ztShop['goodid'])->select();
        $ztGood = Db::name('shopzt')->where('type', '1')->find();
        $ztGood['good'] = Db::name('good')->where('id', 'in', $ztGood['goodid'])->select();
        $this->assign('zttype', $ztType);
        $this->assign('ztshop', $ztShop);
        $this->assign('ztgood', $ztGood);

        $this->assign('item', $item);
        $this->assign('ztallgood', $ztAllGood);
        $this->assign('meta_title', '专题管理');

        return $this->fetch();
    }

    public function addzttype()
    {
        if (request()->isPost()) {
            $data = $_POST;
            $data['addtime'] = time();
            if ($data['id']) {
                Db::name('shopzt')->where('id', $data['id'])->update($data);
            } else {
                Db::name('shopzt')->insert($data);
            }
            $this->success('操作成功');
        }
    }

    public function addgood()
    {
        if (request()->isPost()) {
            $gid = input('id/a');
            $d['goodid'] = implode(',', $gid);
            $newid = input('type');
            $d['addtime'] = time();
            Db::name('shopzt')->where('id', $newid)->update($d);
            $this->success('操作成功', 'coupon/zt');
        }
        $cateid = input('cateid');
        $cateid1 = input('cateid1');
        $category1 = [];
        if ($cateid) {
            $map['catid'] = $cateid;
            $category1 = Db::name('category')->where(['is_delete' => 0, 'pid' => $cateid])->select();
            $this->assign('cateid', $cateid);
        }
        if ($cateid1) {
            $map['catid'] = $cateid1;

            $this->assign('cateid1', $cateid1);
        }
        $keyword = input('keyword');
        if ($keyword) {
            $map['title'] = ['like', '%'.(string) $keyword.'%'];
        }
        $map['is_delete'] = '1';
        $goodList = $this->lists('Good', $map, 'id desc');
        $this->assign('_list', $goodList);
        $category = Db::name('category')->where(['is_delete' => 0, 'pid' => 0])->select();
        $this->assign('category', $category);
        $this->assign('category1', $category1);
        $this->assign('meta_title', '选择专题商品');

        return $this->fetch();
    }

    public function addshop()
    {
       
            if (request()->isPost()) {
                $gid = input('id/a');
                $d['goodid'] = implode(',', $gid);
                $newid = 2;
                $d['addtime'] = time();
                Db::name('shopzt')->where('id', $newid)->update($d);
                $this->success('操作成功', 'coupon/zt');
            }
           
        $map['sid'] = ['>', '0'];
        $goodList = $this->lists('supuser', $map, 'sid desc');
        $this->assign('_list', $goodList);

        return $this->fetch();
    }
}
