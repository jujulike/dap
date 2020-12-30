<?php

// 本类由系统自动生成，仅供测试用途

namespace app\apishop\controller;

use Firebase\JWT\JWT;
use think\Controller;
use think\Db;

class Supbase extends Controller
{
    //构造函数
    public function _initialize()
    {
        //php 判断http还是https
        $pravetkey = '121232142';
        $this->onepagenum = 6;
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        //所有图片路径
        define(__DATAURL__, $http_type.$_SERVER['SERVER_NAME']);
        define(__UEDITORURL__, $http_type.$_SERVER['SERVER_NAME'].'/ueditor/');
        define(__HTTP__, $http_type);
        $header = $this->request->header();
        $token = $header['token'];
        if (!$token) {
            _internalError('错误', 501, '');
        }

        try {
            JWT::decode($token, $pravetkey, ['HS256']);
        } catch (\Exception $e) {
            // return false;

            _internalError($e->getMessage(), 501, '');
            //echo $e->getMessage();
             die(); // 终止异常
        }
        $t = JWT::decode($token, $pravetkey, ['HS256']);
        $this->sid = $t->data;
        $this->time = $t->exp;
        if ($this->time < time()) {
            _internalError('登陆超时', 501, '');
        }
        $shop = Db::name('supuser')->where('sid', $this->sid)->find();
        if (!$shop) {
            _internalError('信息错误', 501, '');
        }
    }

    /**
     *@return int 返回唯一订单号
     */
    public function randStr($length = 4, $type = 'number')
    {
        $array = [
            'number' => '0123456789',
            'string' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'mixed' => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        ];
        $string = $array[$type];
        $count = strlen($string) - 1;
        $rand = '';
        for ($i = 0; $i < $length; ++$i) {
            $rand .= $string[mt_rand(0, $count)];
        }

        return $rand;
    }

    public function score($user_id, $note, $price)
    {
        //积分获得
        $web_score = Db::name('info')->where('id', 1)->value('score');
        $score = intval($price * $web_score);
        if ($score) {
            $rs = ['user_id' => $user_id, 'note' => $note, 'score' => $score, 'addtime' => time(), 'sent' => 1];
            Db::name('score_act')->insert($rs);
            Db::name('user')->where(['uid' => $user_id])->setInc('totalscore', $score);
        }
    }

    //上传图片
    public function uploadc()
    {
        if ($_FILES['file']['name']) {
            $path = $this->upfile('file');
            $imgurl = '/uploads/user/imgs/'.$path;
        }
        if ($imgurl) {
            $data = [
                'img' => $imgurl,
                'status' => 1,
            ];
            _success('上传成功', 200, $data);

            return json(['code' => 1, 'msg' => '上传成功', 'data' => $data], 200);
        } else {
            _internalError('上传失败', 500, '');

            return json(['code' => 0, 'msg' => '上传失败'], 200);
        }
    }

    public function upfile($arr)
    {
        $file = request()->file($arr);
        $info = $file->move(ROOT_PATH.'/public/uploads/user/imgs');
        if ($info) {
            $dd = $info->getSaveName();
            $t = str_replace('\\', '/', $dd);

            return $t;
        } else {
            return false;
        }
    }
}
