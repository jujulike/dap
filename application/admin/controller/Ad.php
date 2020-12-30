<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2018/9/21
 * Time: 15:23.
 */

namespace app\admin\controller;

use think\Db;

class Ad extends Admin
{
    public function index()
    {
        $map['id'] = ['>', '0'];
        $list = $this->lists('adtype', $map, 'id asc');
        $this->assign('list', $list);
        $this->assign('types', 'all');
        $this->assign('meta_title', '广告位');

        return $this->fetch();
    }

    /*
     *
     * 添加，修改广告位
     */
    public function adtype()
    {
        $id = input('id');

        if ($id) {
            $meta_title = '广告位编辑';
            $adtype = Db::name('adtype')->where('id', $id)->find();
            $wh['id'] = ['neq', $id];
            $wh['name'] = $_POST['name'];
            if ($adtype['name'] == $_POST['name']) {
                $this->error('操做成功,您未作任何修改。');
            }
            $this->assign('adtype', $adtype);
        } else {
            $meta_title = '添加广告位';
            $wh['name'] = $_POST['name'];
        }

        $this->assign('meta_title', $meta_title);
        if (request()->isPost()) {
            $data = $_POST;
            $adin = Db::name('adtype')->where($wh)->find();
            if ($adin) {
                $this->error('改广告位已经存在，广告位不可重复。');
            }
            if ($id) {
                $res = Db::name('adtype')->where('id', $id)->update($data);
            } else {
                $res = Db::name('adtype')->insertGetId($data);
            }
            if ($res) {
                $this->success('操做成功', 'ad/index');
            } else {
                $this->error('操做失败');
            }
        }

        return $this->fetch();
    }

    /*
     * 广告管理
     * */
    public function bander()
    {
        $map['id'] = ['>', '0'];
        $banner = $this->lists('Ad', $map, 'id desc');
        foreach ($banner as $key => $val) {
            if ($val['tid']) {
                $groups = Db::name('adtype')->where('id', $val['tid'])->find();
                $banner[$key]['typename'] = $groups['name'];
            }
        }
        $this->assign('banner', $banner);
        $this->assign('meta_title', '广告管理');

        return $this->fetch();
    }

    /*
     *
     * 广告添加修改
     */
    public function upbanner()
    {
        $adtype = Db::name('adtype')->select();
        $this->assign('adtype', $adtype);
        $category = $this->getmypidcate();
        $this->assign('category', $category);

        $aid = input('id');
        if (request()->isPost()) {
            $id = $_POST['id'];
            $arr = $_POST;
            if ($_FILES['banner']['name']) {
                $path = $this->upfile('banner');
                $arr['img'] = '/upload/images/'.$path;
            }
            if ($id) {
                $arr['id'] = $id;
                $up = Db::name('ad')->where('id', $arr['id'])->update($arr);
                if ($up) {
                    if ($arr['types'] == 1) {
                        $this->success('修改成功', url('ad/bander'));
                    } else {
                        $this->success('修改成功', url('ad/bander'));
                    }
                } else {
                    $this->error('修改失败');
                }
            } else {
                $arr['addtime'] = time();
                $arr['endtime'] = time() + 100 * 24 * 60 * 60;
                $ins = Db::name('ad')->insert($arr);
                if ($ins) {
                    if ($arr['types'] == 1) {
                        $this->success('添加成功', url('ad/bander'));
                    } else {
                        $this->success('添加成功', url('ad/bander'));
                    }
                } else {
                    $this->error('添加失败');
                }
            }
        } else {
            if ($aid) {
                $banner = Db::name('ad')->where('id', $aid)->find();
                $this->assign('meta_title', '轮播图编辑');
                $this->assign('banner', $banner);

                return  $this->fetch();
            } else {
                $this->assign('meta_title', '轮播图添加');

                return  $this->fetch();
            }
        }
    }

    //删除广告及广告位
    public function delban()
    {
        $id = input('id');
        $dbname = input('dbname') ? input('dbname') : 'ad';
        $res = Db::name($dbname)->where(['id' => $id])->delete();
        if ($res) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function sou()
    {
        $keyword = input('keyword');
        if ($keyword) {
            $map['name'] = ['like', '%'.(string) $keyword.'%'];
        }

        $map['is_delete'] = 0;
        $status = input('status');

        if (!empty($status)) {
            $map['status'] = ['eq', $status - 1];
            $this->assign('status', $status);
        }

        $map['type'] = 1;
        $list = $this->lists('goodkey', $map, 'id desc');
        foreach ($list as &$val) {
            $val['addtime'] = date('Y-m-d H:i', $val['addtime']);
        }
        $this->assign('list', $list);
        $this->assign('meta_title', '商品关键词');

        return  $this->fetch();
    }

    public function addsou()
    {
        $id = input('id');

        if ($id) {
            $meta_title = '关键词';
            $adtype = Db::name('goodkey')->where('id', $id)->find();
            $wh['id'] = ['neq', $id];
            $wh['name'] = $_POST['name'];
            if ($adtype['name'] == $_POST['name']) {
                $this->error('操做成功,您未作任何修改。');
            }
            $this->assign('adtype', $adtype);
        } else {
            $meta_title = '添加关键词';
            $wh['name'] = $_POST['name'];
        }

        $this->assign('meta_title', $meta_title);
        if (request()->isPost()) {
            $data = $_POST;
            $data['addtime'] = time();
            $adin = Db::name('goodkey')->where($wh)->find();
            if ($adin) {
                $this->error('关键词已经存在，关键词不可重复。');
            }
            if ($id) {
                $res = Db::name('goodkey')->where('id', $id)->update($data);
            } else {
                $res = Db::name('goodkey')->insertGetId($data);
            }
            if ($res) {
                $this->success('操做成功', 'ad/sou');
            } else {
                $this->error('操做失败');
            }
        }

        return  $this->fetch();
    }

    public function tags()
    {
        $data = Db::name('supusertag')->select();
        $this->assign('list', $data);
        $this->assign('meta_title', '商家标签');

        return  $this->fetch();
    }

    public function addtags()
    {
        $id = input('id');

        if ($id) {
            $meta_title = '标签编辑';
            $adtype = Db::name('supusertag')->where('id', $id)->find();
            $wh['id'] = ['neq', $id];
            $wh['name'] = $_POST['name'];
            if ($adtype['name'] == $_POST['name']) {
                $this->error('操做成功,您未作任何修改。');
            }
            $this->assign('adtype', $adtype);
        } else {
            $meta_title = '添加标签';
            $wh['name'] = $_POST['name'];
        }

        $this->assign('meta_title', $meta_title);
        if (request()->isPost()) {
            $data = $_POST;
            $adin = Db::name('supusertag')->where($wh)->find();
            if ($adin) {
                $this->error('标签已经存在，标签不可重复。');
            }
            if ($id) {
                $res = Db::name('supusertag')->where('id', $id)->update($data);
            } else {
                $res = Db::name('supusertag')->insertGetId($data);
            }
            if ($res) {
                $this->success('操做成功', 'ad/tags');
            } else {
                $this->error('操做失败');
            }
        }

        return  $this->fetch();
    }

    public function news()
    {
        $map['id'] = ['>', '0'];
        $list = $this->lists('feedback', $map, 'id asc');
        foreach ($list as $k => $v) {
            if ($v['img_list']) {
                $list[$k]['img_arr'] = explode(',', $v['img_list']);
            }
        }
        $this->assign('list', $list);

        $this->assign('meta_title', '投诉建议');

        return  $this->fetch();
    }
}
