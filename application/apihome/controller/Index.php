<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2020/9/30
 * Time: 14:34.
 */

namespace app\apihome\controller;

use app\common\logic\Supuser;
use app\common\model\Good;
use Firebase\JWT\JWT;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $pravetkey = 'kuaixiaol1210';
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIiLCJpYXQiOjE2MDgwMDI1OTUsImV4cCI6MTYwODYwNzM5NSwianRpIjoiODMxM2Y2N2RlNmRhOTA5NzdkMDc1Y2I4NzAzNjJlN2MiLCJkYXRhIjp7ImlkIjozLCJtb2JpbGUiOjB9fQ.IYoZH9hMYtgemMlkPHsUTk0uSF9-5_63TKIhTPOWYec';

        if (!$token) {
            // $this->uid= 1;
            //  $this->phone=1;

            _internalError('错误', 501, '');
        } else {
            try {
                JWT::decode($token, $pravetkey, ['HS256']);
            } catch (PDOException $e) {
                _internalError($e->getMessage(), 501, '');
                //echo $e->getMessage();
             die(); // 终止异常
            }

            $t = JWT::decode($token, $pravetkey, ['HS256']);
            $dataold = $t->data;
            //$this->uid= 1;

            $this->uid = $dataold->id;
            $this->phone = $dataold->mobile ? $dataold->mobile : 0;
            $this->time = $t->exp;
            if ($this->time < time()) {
                _internalError('登陆超时', 501, '');
            }
        }
        $nowuser = Db::name('user')->where('uid', $this->uid)->find();
        if (!$nowuser) {
            _internalError('信息错误', 501, '');
        }
        $where['sid'] = ['in', '1,2,3,4,5,6'];
        $shoplogic = new Supuser();
        $whereg['catid'] = ['in', '42,76,29'];
        $good = Good::all($whereg)->toArray();
        $list = $shoplogic->getshop($where);
        halt($good);
    }

    public function sendnewcode()
    {
        $phone = '18883776695';
        $code = rand(100000, 999999); //生成6位随机密码
        $alidayu = new \alisms\SendSms();
        // $t = $alidayu->ali_SendCode($phone, $code);
        //$result=$this->send_Code($phone,$code);

        _success('发送成功', '200', $alidayu->ali_SendCode($phone, $code));
    }

    public function send_Code($phone, $code)
    {
        $alidayu = new \alidayu\AliSendCode();
        $alidayu->ali_SendCode($phone, $code);
    }

    public function payorder()
    {
        $out_trade_no = date('YmdHis'); //商户系统内部订单号，要求64个字符内、且在同一个商户号下唯一
        $subject = '测试'; //订单标题
        $total_fee = '1'; //订单总金额，单位为分
        $users = [
            'app_id' => 'wx1b1b4a148bacc3b1',
            'open_id' => 'o1NRJ4ysAXljb2p4A7Jl8C9EZPF4',
        ];
        $users = json_encode($users);
        $hpay = new \Hmoney\Hmoneypay();
        $datas = $hpay->getPay($out_trade_no, $subject, $total_fee, $users);
        $datas = json_decode($datas, 1);
        //exit();
        _success('下单成功', '200', $datas);
        if ($datas['return_code'] == 'SUCCESS' && $datas['result_code'] == 'SUCCESS') {
            $d = [
                     'payurl' => $datas['hy_mini_pay_params'],
                     'order_sn' => $out_trade_no,
                     'paymoney' => $price,
                 ];
            $payurl = json_decode($datas['hy_mini_pay_params'], 1);
            _success('下单成功', '200', $payurl);
        } else {
            _internalError('下单失败');
        }
        _success('发送成功', '200', '');
    }

    public function getstr()
    {
        $out_trade_no = '1608717347_PV6TTZXSKXYVE9';
        $t = substr($out_trade_no, 10);
        echo $t;
    }

    public function queryorder()
    {
        $out_trade_no = '20201118175149';
        $hy_bill_no = '2011181752011299810589001341'; //订单标题

        $hpay = new \Hmoney\Hmoneypay();
        _success('发送成功', '200', $hpay->querryOrder($hy_bill_no, $out_trade_no));
    }

    public function refundorder()
    {
        $out_trade_no = '1608800951QZE3OJQQO4ZSC2';
        $hy_bill_no = date('YmdHis'); //订单标题
        $out_refund_no = date('YmdHis'); //订单标题
        $total_fee = 10;
        $refund_fee = 5;
        $hpay = new \Hmoney\Hmoneypay();
        _success('发送成功', '200', $hpay->refund($out_trade_no, $out_refund_no, $total_fee, $refund_fee));
    }

    public function ledgerAccount()
    {
        //$out_trade_no = '1608800951QZE3OJQQO4ZSC2';
        $hy_bill_no = '2012241709388800310589001393'; //订单标题
        $out_refund_no = date('YmdHis'); //订单标题
        $total_fee = 100;
        $refund_fee = 1;
        $hpay = new \Hmoney\Hmoneypay();
        $data[0] = [
        'login_account' => 'xwsczf',
        'allot_amt_fen' => '1',
        ];
        $data[1] = [
        'login_account' => 'xyspzf',
        'allot_amt_fen' => '1',
        ];
        _success('发送成功', '200', $hpay->ledgerAccount($hy_bill_no, $out_trade_no = '', $data));

        //  {"status":"200","message":"\u53d1\u9001\u6210\u529f","show_data":"{\"return_code\":\"SUCCESS\",\"result_code\":\"SUCCESS\",\"app_id\":\"hyp201109105890000030515F20FB8A4\",\"mch_uid\":\"1058902127209\",\"hy_bill_no\":\"2012241709388800310589001393\",\"out_trade_no\":\"1608800951QZE3OJQQO4ZSC2\",\"return_msg\":\"\u63d0\u4ea4\u5206\u6da6\u8bf7\u6c42\u6210\u529f\",\"allot_amt_fen\":\"9\",\"sign\":\"8CAC9E4EC9C612B90E14EC4400F58F99\"}"}
    }

    public function getshopdetail()
    {
        $sid = _param('sid', '商户信息错误');
        $where['sid'] = $sid;
        $shoplogic = new Supuser();
        $list = $shoplogic->getshopdetail($where);
        $cate = $shoplogic->GetCateroryName('2');
        halt($list);
    }

    public function groupshop()
    {
    }

    public function getshop()
    {
        //获取类型下商品id
        //商品id后查询订单数量
        //以订单数量作为排序准则的
        $tid = _param('tid', '分类不能为空');
        $catid = Db::name('category')->where('pid', 'in', $tid)->select();
        $tids = implode(',', array_column($catid, 'id'));
        $good = Db::name('good')->where('catid', 'in', $tids)->select();
        if (!$good) {
            _success('数据为空', '200', '');
        }
        $gid = implode(',', array_column($good, 'id'));
        $where = ' 1=1 ';
        $rs = Db::name('supuser')->order('rand()')->select();
        // $where .= " and o.goodid in (".$gid.")";
        /* $rs = Db::name('supuser')
            ->alias('s')
            ->join('shop_order_good o ', 'o.sid=s.sid')
            ->field("s.*,o.num,o.sid,o.goodid,sum(o.num) as count ").
             //->fetchSql(true);
            ->select();*/
        $data = [
           'shop' => $rs,
           'status' => 1,
           'webtitle' => '店铺列表',
       ];
        _success('获取成功', 200, $data);
    }

    public function _empty()
    {
    }

    /*
      * 商品详情
      */
    public function getgooddet($gid)
    {
        $good = Db::name('good')->where('id', $gid)->find();
        if (!$good) {
            return '';
        }
        $n = [];
        if ($good['photo_x']) {
            $img2 = explode(',', $good['photo_x']);
            foreach ($img2 as $k => $v) {
                $n[] = __DATAURL__.$v;
            }
        }
        $good['piccs'] = $n; //图片集1
        //图片轮播数组
        $img = explode(',', trim($good['photo_string'], ','));
        $b = [];
        if ($good['photo_string']) {
            foreach ($img as $k => $v) {
                if (strpos($v, 'http') !== false) {
                    $b[] = $v;
                } else {
                    $b[] = __DATAURL__.$v;
                }
            }
        } else {
            $b[] = '';
        }
        $good['img_arr'] = $b; //图片轮播数组
        $content = str_replace('/ueditor/', __UEDITORURL__, $good['content']);
        $good['content'] = $content;

        return $good;
    }
}
