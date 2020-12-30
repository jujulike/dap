<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2020/10/21
 * Time: 11:56.
 */

namespace app\apihome\controller;

use think\Db;

class Message extends Home
{
    //获取用户系统消息
    public function getusermessage()
    {
        $map1['sid'] = 0;
        $uid = $this->uid;
        $info = Db::name('messagesys')->where($map1)
        ->where('uid', 'like', ['%'.$uid, $uid.'%'], 'OR')
        ->select();
        $rows = [
            'message' => $info,
        ];
        _success('获取成功', '', $rows);
    }

    public function index()
    {
        /*
         * 根据fromid来获取当前用户聊天列表
         */

        $map1['toid'] = $this->uid;
        $map1['status'] = 2; //商家对用户
        $map2['fromid'] = $this->uid;
        $map2['status'] = 1; //用户对商家
        $str = [$map2, $map1, 'or'];

        /**$info = Db::name('message')->where(function ($query) {
                 $map1['toid'] = $this->uid;
                 $map1['status'] = 2;//商家对用户
                 $query->where($map1);
                 })->whereOr(function ($query) {
                 $map2['fromid']=$this->uid;
                 $map2['status']=1;//用户对商家
                 $query->where($map2);
                 })->group('toid')
                 ->select();
**/
        $info = Db::name('message')->where($map1)->group('fromid')
            ->select();

        $rows = array_map(function ($res) {
            return [
                'shophead_url' => $this->getshophead($res['fromid']),
                'shopname' => $this->getshopname($res['fromid']),
                'countNoread' => $this->getCountNoread($res['fromid'], $res['toid']),
                'last_message' => $this->getLastMessage($res['toid'], $res['fromid']),
                'chat_page' => "http://www.zfc2020kxl.com/index/index/medetail?uid={$res['toid']}&toid={$res['fromid']}",
            ];
        }, $info);
        _success('获取成功', '', $rows);
        //  return $rows;
    }

    /*
     *保存消息
     */
    public function save_message()
    {
        $sid = _param('toid');
        $s = _param('s');
        if ($s != 1) {
            return '';
        }

        $datas['fromid'] = $this->uid;
        $datas['fromname'] = $this->getName($datas['fromid']);
        $datas['toid'] = $sid;
        $datas['toname'] = $this->getshopName($datas['toid']);
        $datas['content'] = _param('data');
        $datas['addtime'] = _param('time');
        $datas['isread'] = 0;
        $datas['type'] = 1;
        $datas['status'] = $s;
        $rs = Db::name('message')->insert($datas);
        if ($rs) {
            _success('发送成功');
        }
    }

    /**
     * 页面加载返回聊天记录.
     */
    public function loadmessage()
    {
        $pagenum = 10;
        $shopid = _param('toid');
        $userid = $this->uid;
        $page = _param('page', '', '1', false);

        $map1['fromid'] = $shopid;
        $map1['toid'] = $userid;
        $map1['status'] = 2; //商家对用户
        $map2['toid'] = $shopid;
        $map2['fromid'] = $userid;
        $map2['status'] = 1; //用户对商家

        $count = Db::name('message')->where(function ($query) use ($map1) {
            $query->where($map1);
        })->whereOr(function ($query) use ($map2) {
            $query->where($map2);
        })->count('id');
        $limit = intval($page - 1) * 10;
        $message = Db::name('message')->where(function ($query) use ($map1) {
            $query->where($map1);
        })->whereOr(function ($query) use ($map2) {
            $query->where($map2);
        })->order('id desc')->limit($limit.','.$pagenum)->select();

        $message = array_reverse($message);
        _success('获取成功', '', $message);
    }

    public function get_head()
    {
        $uid = $this->uid;
        $sid = _param('toid');
        $data = [
            'from_head' => $this->getuserhead($uid),
            'to_head' => $this->getshophead($sid),
        ];
        _success('获取成功', '', $data);
    }

    public function changeNoRead()
    {
        $fromid = input('toid');
        $toid = input('fromid');
        Db::name('message')->where(['fromid' => $fromid, 'toid' => $toid])->update(['isread' => 1]);
    }

    public function get_name()
    {
        $sid = _param('uid');
        $name = $this->getshopname($sid);
        _success('获取成功', '', $name);
    }

    //获取商家头像
    public function getshophead($sid)
    {
        $fromhead = Db::name('supuser')->where('sid', $sid)->field('headimgurl')->find();
        $pic = 'http://www.zfc2020kxl.com'.'/zhiing/home/images/plimg.jpg';
        if ($fromhead['headimgurl']) {
            $pic = __DATAURL__.$fromhead['headimgurl'];
        }

        return $pic;
    }

    //获取商家姓名
    public function getshopname($sid)
    {
        $fromhead = Db::name('supuser')->where('sid', $sid)->field('shopname')->find();

        return $fromhead['shopname'];
    }

    //获取用户名字
    public function getName($uid)
    {
        $fromhead = Db::name('user')->where('uid', $uid)->field('nickname')->find();

        return $fromhead['nickname'];
    }

    //获取用户头像
    public function getuserhead($uid)
    {
        $fromhead = Db::name('user')->where('uid', $uid)->field('headimgurl')->find();
        $pic = 'http://www.zfc2020kxl.com'.'/zhiing/newcj/img/123.jpg';
        if ($fromhead['headimgurl']) {
            $pic = __DATAURL__.$fromhead['headimgurl'];
        }

        return $pic;
    }

    public function getCountNoread($fromid, $toid)
    {
        return Db::name('message')->where(['fromid' => $fromid, 'toid' => $toid, 'isread' => 0])->count('id');
    }

    public function getLastMessage($shopid, $uid)
    {
        $map1['fromid'] = $shopid;
        $map1['toid'] = $uid;
        $map1['status'] = 2; //商家对用户
        $map2['toid'] = $shopid;
        $map2['fromid'] = $uid;
        $map2['status'] = 1; //用户对商家

        $map1 = "  (fromid='".$shopid."'"."and toid='".$uid."'".'and status=1)';
        $map1 .= "or (fromid='".$uid."'"."and toid='".$shopid."'".'and status=2)';
        $info = Db::name('message')->where($map1)->order('id DESC')->limit(1)->find();

        return $info;
    }
}
