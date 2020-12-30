<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2020/10/21
 * Time: 11:56.
 */

namespace app\apishop\controller;

use think\Db;

class Message extends Supbase
{
    public function index3()
    {
        echo '111';
    }

    public function index()
    {
        /*
         * 根据fromid来获取当前用户聊天列表
         */

        $map1['toid'] = $this->sid;
        $map1['status'] = 1; //用户对商家

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
                'shophead_url' => $this->getuserhead($res['fromid']),
                'shopname' => $this->getName($res['fromid']),
                'countNoread' => $this->getCountNoread($res['fromid'], $res['toid']),
                'last_message' => $this->getLastMessage($res['fromid'], $res['toid']),
                'chat_page' => "/index/shop/medetail?sid={$res['toid']}&toid={$res['fromid']}",
            ];
        }, $info);
        _success('获取成功', '', $rows);
        //  return $rows;
    }

    public function getshopmessage()
    {
        $map1['uid'] = 0;
        $sid = $this->sid;
        $info = Db::name('messagesys')->where($map1)
        ->where('sid', 'like', ['%'.$sid, $sid.'%'], 'OR')
        ->select();
        $rows = [
            'message' => $info,
        ];
        _success('获取成功','', $rows);
    }

    /*
     *保存消息
     */
    public function save_message()
    {
        $uid = _param('toid');
        $status = _param('s');
        if ($status != 2) {
            return '';
        }
        $datas['fromid'] = $this->sid;
        $datas['fromname'] = $this->getshopName($datas['fromid']);
        $datas['toid'] = $uid;
        $datas['toname'] = $this->getName($datas['toid']);
        $datas['content'] = _param('data');
        $datas['addtime'] = _param('time');
        $datas['isread'] = 0;
        $datas['type'] = 1;
        $datas['status'] = $status;
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
        $shopid = $this->sid;
        $userid = _param('toid');
        $page = _param('page', '', '1', false);

        $map1['fromid'] = $shopid;
        $map1['toid'] = $userid;
        $map1['status'] = 2; //商家对用户

        $map2['toid'] = $shopid;
        $map2['fromid'] = $userid;
        $map2['status'] = 1; //用户对商家
        $limit = intval($page - 1) * 10;

        $message = Db::name('message')->where(function ($query) use ($map1) {
            $query->where($map1);
        })->whereOr(function ($query) use ($map2) {
            $query->where($map2);
        })->order('id desc')->limit($limit.','.$pagenum)->select();

        $count = Db::name('message')->where(function ($query) use ($map1) {
            $query->where($map1);
        })->whereOr(function ($query) use ($map2) {
            $query->where($map2);
        })->count('id');
        $message = array_reverse($message);
        _success('获取成功', 200, $message);
    }

    public function get_head()
    {
        $uid = _param('toid');
        $sid = $this->sid;
        $data = [
            'to_head' => $this->getuserhead($uid),
             'from_head' => $this->getshophead($sid),
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
        $name = $this->getName($sid);
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

    public function getuserhead($uid)
    {
        $fromhead = Db::name('user')->where('uid', $uid)->field('headimgurl')->find();
        $pic = 'http://www.zfc2020kxl.com'.'/zhiing/home/images/plimg.jpg';
        if ($fromhead['headimgurl']) {
            $pic = __DATAURL__.$fromhead['headimgurl'];
        }

        return $pic;
    }

    public function getshopname($sid)
    {
        $fromhead = Db::name('supuser')->where('sid', $sid)->field('shopname')->find();

        return $fromhead['shopname'];
    }

    public function getName($uid)
    {
        $fromhead = Db::name('user')->where('uid', $uid)->field('nickname')->find();

        return $fromhead['nickname'];
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
