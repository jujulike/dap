<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2018/9/19
 * Time: 16:55
 */

namespace app\admin\controller;

use think\Db;

class Myuser extends Admin
{
    //会员列表
    public function index()
    {
        $nickname = input('nickname');
        //$map['status'] = array('egt', 0);
        if ($nickname) {
            /*if (is_numeric($nickname)) {
            $map['uid'] = intval($nickname);
            } else {}*/
            $map['nickname'] = array('like', '%' . (string) $nickname . '%');

        }
        $name = input('name');
        if ($name) {
            $map['realname'] = array('like', '%' . (string) $name . '%');
        }
        $mobile = input('mobile');
        if ($mobile) {
            $map['mobile'] = array('like', '%' . (string) $mobile . '%');
        }
        $typa = input('typa') ? input('typa') : 0;
        if ($typa == 2) {
            $map['istype'] = array('neq', 0);

        } else {
            $map['istype'] = 0;
        }
        $this->assign('types', $typa);
        $map['openid'] = array('neq', 'undefined');
        $btns          = input('btns');
        if ($btns == 'explode') {
            $list = Db::name("User")->where($map)->order("id desc")->select();
            if (!$list) {$this->error('数据为空');}
            /* 输入到CSV文件 */
            $html = "\xEF\xBB\xBF";

            /* 输出表头 */
            $filter = array(
                'uid'        => 'UID',
                'realname'   => '真实姓名',
                'mobile'     => '手机号',
                'nickname'   => '昵称',
                'totalprice' => '余额',
                'totalscore' => '积分',
                'addtime'    => '注册时间',
                'istype'     => '状态',
            );

            foreach ($filter as $key => $title) {
                $html .= $title . "\t,";
            }
            $html .= "\n";
            foreach ($list as $k => $v) {
                foreach ($filter as $key => $title) {
                    $items = '';
                    if ($key == 'istype') {
                        if ($v['istype'] == 1) {
                            $html .= "核销员\t, ";
                        } else {
                            $html .= "普通会员\t, ";
                        }
                    } else if ($key == 'addtime') {
                        if ($v['addtime']) {
                            $html .= date('Y-m-d H:i', $v['addtime']) . "\t, ";
                        } else {
                            $html .= "\t, ";
                        }
                    } else {
                        $html .= $v[$key] . "\t, ";
                    }
                }
                $html .= "\n";
            }
            /* 输出CSV文件 */
            header("Content-type:application/vnd.ms-csv");
            header("Content-Disposition:attachment; filename=用户信息.csv");
            echo $html;
            exit();

        }
        $isorder = input("isorder/d");
        if ($isorder) {
            $order = "totalprice desc,uid desc";
        } else {
            $order = "uid desc";
        }

        $list = $this->lists('User', $map, $order);
        foreach ($list as &$v) {
            $level      = "普通会员";
            $v['jbname'] = $level;
        }
        $this->assign('isorder', $isorder);
        $this->assign('list', $list);
        $this->assign('meta_title', '会员管理');
        return $this->fetch();
    }
    public function export()
    {
        $id = input('id/a');
        if ($id) {
            $map['uid'] = array('in', $id);
        } else {
            $map['uid'] = array('>', '0');
        }

        $list = $this->lists('User', $map);

        $this->assign('list', $list);
        $this->assign('title', '用户列表');
        return $this->fetch();
    }
    //余额明细
    public function balance()
    {
        $user_id        = intval(input('user_id'));
        $map['user_id'] = $user_id;
        $sent           = intval($_GET['sent']);
        if ($sent) {
            $map['sent'] = $sent - 1;
        }
        $list = $this->lists('balance_act', $map, 'id desc');
        foreach ($list as &$v) {
            $user          = Db::name("user")->where('uid', $v['user_id'])->find();
            $v['realname'] = $user['realname'];
            $v['mobile']   = $user['mobile'];
            $v['nickname'] = $user['nickname'];
        }
        $this->assign('user_id', $user_id);
        $this->assign('sent', $sent);
        $this->assign('list', $list);
        $this->assign('title', '余额明细');
        return $this->fetch();
    }

    public function deluser()
    {

        return $this->fetch();
    }
    public function info()
    {
        $userid   = input('uid');
        $userinfo = Db::name('user')->where('uid', $userid)->find();
        $this->assign('meta_title', '用户详情');

        //$meth['a']=substr_count($userinfo['istype'],'1')?1:0;
        $meth['b'] = substr_count($userinfo['istype'], '1') ? 1 : 0;
        $meth['c'] = substr_count($userinfo['istype'], '2') ? 2 : 0;
        $meth['d'] = substr_count($userinfo['istype'], '3') ? 3 : 0;
        $meth['e'] = substr_count($userinfo['istype'], '4') ? 4 : 0;
        $meth['f'] = substr_count($userinfo['istype'], '5') ? 5 : 0;
        $meth['g'] = substr_count($userinfo['istype'], '6') ? 6 : 0;
        $result    = explode(',', $userinfo['istype']);
        if ($result[0] == 0) {
            $this->assign('puy', 1);
        }
        $this->assign('userinfo', $userinfo);
        $this->assign('meth', $meth);
        return $this->fetch();
    }
    //等级
    public function level()
    {
        if (request()->isPost()) {
            $rebate              = $_POST['rebate'];
            $data['levelrebate'] = json_encode($rebate);
            Db::name("Info")->where("id", 1)->update($data);
            $this->success('操作成功');
        }
        $levelrebate = Db::name("Info")->where("id", 1)->value("levelrebate");
        $rebate      = json_decode($levelrebate, true);
        $this->assign('rebate', $rebate);
        $this->assign('meta_title', '会员等级');
        return $this->fetch();
    }
    /* @param string $model 模型名称,供D函数使用的参数*/
    //状态修改
    public function setpropertya($model)
    {
        $id   = input('id');
        $type = input('type');
        $data = input('data');
        if ($type) {
            $data = ($data == 1 ? '0' : '1');
            Db::name($model)->where(array("uid" => $id))->update(array($type => $data));
            exit(json_encode(array("result" => 1, "data" => $data)));
        }
        die(json_encode(array("result" => 0)));
    }
    //导出用户数据
    public function export_data()
    {
        //清除购买卡却未付款的订单
        $nickname = input('nickname');
        //$map['status'] = array('egt', 0);
        if ($nickname) {
            /*if (is_numeric($nickname)) {
            $map['uid'] = intval($nickname);
            } else {}*/
            $map['nickname'] = array('like', '%' . (string) $nickname . '%');

        }
        $name = input('name');
        if ($name) {
            $map['realname'] = array('like', '%' . (string) $name . '%');
        }
        $mobile = input('mobile');
        if ($mobile) {
            $map['mobile'] = array('like', '%' . (string) $mobile . '%');
        }
        $map['openid'] = array('neq', 'undefined');
        $lists         = Db::name('User')->where($map)->order('uid desc')->select();
        //$lists = $this->lists('User', $map,'uid desc');
        if (!$lists) {
            $this->error("没有数据可导出");
        }

        /* 输入到CSV文件 */
        $html = "\xEF\xBB\xBF";

        /* 输出表头 */
        $filter = array(
            'uid'        => 'UID',
            'realname'   => '真实姓名',
            'mobile'     => '手机号',
            'nickname'   => '昵称',
            'totalprice' => '余额',
            'totalscore' => '积分',
            'addtime'    => '注册时间',
            'istype'     => '状态',
        );

        foreach ($filter as $key => $title) {
            $html .= $title . "\t,";
        }
        $html .= "\n";
        foreach ($lists as $k => $v) {
            foreach ($filter as $key => $title) {
                $items = '';
                if ($key == 'istype') {
                    if ($v['istype'] == 1) {
                        $html .= "已是核销员\t, ";
                    } else {
                        $html .= "待成为核销员\t, ";
                    }
                } else if ($key == 'addtime') {
                    if ($v['addtime']) {
                        $html .= date('Y-m-d H:i', $v['addtime']) . "\t, ";
                    } else {
                        $html .= "\t, ";
                    }
                } else {
                    $html .= $v[$key] . "\t, ";
                }
            }
            $html .= "\n";
        }
        /* 输出CSV文件 */
        header("Content-type:application/vnd.ms-csv");
        header("Content-Disposition:attachment; filename=用户信息.csv");
        echo $html;
        exit();
    }
    //设置会员类型
    public function set_type()
    {
        $istype = $_POST['istype'];
        $uid    = $_POST['uid'];
        if ($uid > 0) {

            if (count($istype) > 0) {
                for ($i = 0; $i < count($istype); $i++) {
                    if ($istype[$i] > 0) {
                        $data['istype'] .= $istype[$i] . ',';
                    }

                }
            } else {
                $data['istype'] = 0;
            }

            //var_dump($data);exit;
            $res = Db::name('user')->where('uid', $uid)->update($data);

            $this->success('操作成功', 'myuser/index');

        } else {

            $this->err('操作失败');
        }

    }
}
