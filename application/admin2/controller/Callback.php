<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-11-08
 * Time: 9:22
 */

namespace app\home\controller;
use think\Db;
use think\Session;
use alpay\Alinotify;
use wxpay\Notify;
use unionpay\sdk\AcpService;
use unionpay\sdk\LogUtil;

class Callback extends Base
{
    /*
     *
     * 支付宝回调订单处理
     */
    public function alinotify(){
        $arr=$_POST;
        $notify= new Alinotify();
        $result = $notify->rsaCheck($_POST, $_POST['sign_type']);
        pay_log_z(var_export($result,true),'支付宝01');
        if ($result) {
            $ordersn=$_POST['out_trade_no'];
            $this->ret_order($ordersn,'alpay');
        } else {
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),'支付宝');
            echo 'error';
            exit ();
        }
    }

    /*
     *
     * 支付宝回调订单处理【仓位购买】
     */
    public function alinotify_cw(){
        $arr=$_POST;
        $notify= new Alinotify();
        $result = $notify->rsaCheck($_POST, $_POST['sign_type']);
        pay_log_z(var_export($result,true),'支付宝');
        if ($result) {
            $ordersn=$_POST['out_trade_no'];
            $this->way_order($ordersn,'alpay');
        } else {
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),'支付宝');
            echo 'error';
            exit ();
        }
    }

    /*
     *
     * 微信回调订单处理
     */
    public function notifyUrl(){
        /*写入接口日志*/
        $notify= new Notify();
        $result=$notify->notify();
        pay_log_z(var_export($result,true),'微信');
        if ($result) {
            $ordersn=$result['out_trade_no'];
            $this->ret_order($ordersn,'wxpay');

        } else {
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),'微信');
            echo 'error';
            exit ();
        }
    }

    /*
    *
    * 微信回调订单处理【仓位购买】
    */
    public function notifyUrl_cw(){
        /*写入接口日志*/
        $notify= new Notify();
        $result=$notify->notify();
        pay_log_z(var_export($result,true),'微信');
        if ($result) {
            $ordersn=$result['out_trade_no'];
            $this->way_order($ordersn,'wxpay');

        } else {
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),'微信');
            echo 'error';
            exit ();
        }
    }


    /*
    *
    * 银联回调订单处理
    */
    public function backReceive(){
        $AcpService=new AcpService();
        $LogUtil=new LogUtil();
        $logger = $LogUtil::getLogger();
        $logger->LogInfo("receive back notify: " . \unionpay\sdk\createLinkString( $_POST, false, true));

        if($AcpService::validate ($_POST)){
            $ordersn = $_POST ['orderId']; //其他字段也可用类似方式获取
            $respCode = $_POST ['respCode'];
            $this->ret_order($ordersn,'yinlian');
        }else {
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),'银联');
            echo 'error';
            exit ();
        }
    }

    /*
     *
     * 银联回调订单处理【仓位购买】
     */
    public function backReceive_cw(){
        $AcpService=new AcpService();
        $LogUtil=new LogUtil();
        $logger = $LogUtil::getLogger();
        $logger->LogInfo("receive back notify: " . \unionpay\sdk\createLinkString( $_POST, false, true));

        if($AcpService::validate ($_POST)){
            $ordersn = $_POST ['orderId']; //其他字段也可用类似方式获取
            $respCode = $_POST ['respCode'];
            $this->way_order($ordersn,'yinlian');
        } else {
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),'银联');
            echo 'error';
            exit ();
        }
    }

    /*
     *
     * 银联同步返回状态地址[仓位]
     */
    public function frontReceive_cw(){
        $AcpService=new AcpService();
        $LogUtil=new LogUtil();
        $logger = $LogUtil::getLogger();
        $logger->LogInfo("receive back notify: " . \unionpay\sdk\createLinkString( $_POST, false, true));

        $order='';
        if($AcpService::validate ($_POST)){
            $ordersn = $_POST ['orderId']; //其他字段也可用类似方式获取
            $respCode = $_POST ['respCode'];
            $order=Db::name('waybill_order')->where('order_sn',$ordersn)->find();
            if($order['pay_status']==1){
                //已支付
                $if_order=1;
            }else{
                //支付失败
                $if_order=0;
            }
        } else {
            $if_order=-1;
        }
        $this->assign('order',$order);
        $this->assign('if_order',$if_order);//订单是否存在
        return $this->fetch();
    }

    /*
     *
     * 银联同步返回状态地址【商品订单】
     */
    public function frontReceive(){
        $AcpService=new AcpService();
        $LogUtil=new LogUtil();
        $logger = $LogUtil::getLogger();
        $logger->LogInfo("receive back notify: " . \unionpay\sdk\createLinkString( $_POST, false, true));

        $order='';
        if($AcpService::validate ($_POST)){
            $ordersn = $_POST ['orderId']; //其他字段也可用类似方式获取
            $respCode = $_POST ['respCode'];
            $order=Db::name('order')->where('order_sn',$ordersn)->find();
            if($order['pay_status']==1){
                //已支付
                $if_order=1;
            }else{
                //支付失败
                $if_order=0;
            }
        } else {
            $if_order=-1;
        }
        $this->assign('order',$order);
        $this->assign('if_order',$if_order);//订单是否存在
        return $this->fetch();
    }

    /*
     * 余额支付
     * */
    public function balance(){
        $param = input('post.');
        $user=Session::get('nowuser');
        $uid=$user['uid'];

        if(!$param['code']){
            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，请填写手机验证码']);
        }
        $usercode=  Session::get('usercode');
        if($usercode!=$param['code']){
            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，手机验证码错误']);
        }
        unset($param['code']);

        $map_order['id']=$param['oid'];
        $map_order['order_sn']=$param['order_sn'];
        $order_count=Db::name('order')->where($map_order)->count();
        $order=Db::name('order')->where($map_order)->find();
        if($order_count=='1' && $order['pay_status']=='0'){
            $money=userMoney($uid);
            if($money){
                if($money['nowmoney_kt']>=$order['price']){

                    $data_zf['uid'] = $user['uid'];
                    $data_zf['sr_uid'] = 0;
                    $data_zf['money'] = $order['price'];
                    $data_zf['phone'] = $user['phone'];
                    $data_zf['type_cz'] = 0;
                    $data_zf['check'] = 0;
                    $data_zf['realmoney'] = 0;
                    $data_zf['status'] = 1;
                    $data_zf['addtime'] = time();
                    $data_zf['endtime'] = time();
                    $url=url('member/details',array('orderid'=>$order['id']));
                    $data_zf['describe'] = '您于' . date("Y/m/d H:i:s") . '使用余额购买商品消费<em style="color: red;">￥'.$order['price'].'</em>，订单号：<a target="_blank" href="'.$url.'" style="color: red;">'.$order['order_sn'].'</a>';
                    $ad_af = Db::name('getmoney')->insert($data_zf);
                    if($ad_af){
                        $order_pay=$this->ret_order($order['order_sn'],'wxpay');
                        if($order_pay===true){
                            $messs1='恭喜您余额支付成功,订单号为:'.$order['order_sn'];
                            sendordermessage($uid,$order['id'],$messs1);
                            return json(['status' => 100, 'data' => '', 'msg' => '支付成功']);
                        }else{
                            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，订单状态修改失败。']);
                        }
                    }else{
                        return json(['status' => 400, 'data' => '', 'msg' => '支付失败，订单状态修改失败。']);
                    }

                }else{
                    return json(['status' => 400, 'data' => '', 'msg' => '用户可资金不足，可用其它方式支付']);
                }

            }else{
                return json(['status' => 400, 'data' => '', 'msg' => '支付失败，用户信息有误。']);
            }
        }else{
            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，订单信息有误。']);
        }
    }


    /*
     * 余额支付仓位购买
     * */
    public function balance_cw(){
        $param = input('post.');
        $user=Session::get('nowuser');
        $uid=$user['uid'];

        if(!$param['code']){
            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，请填写手机验证码']);
        }
        $usercode=  Session::get('usercode');
        if($usercode!=$param['code']){
            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，手机验证码错误']);
        }
        unset($param['code']);

        $map_order['id']=$param['oid'];
        $map_order['order_sn']=$param['order_sn'];
        $order_count=Db::name('waybill_order')->where($map_order)->count();
        $order=Db::name('waybill_order')->where($map_order)->find();
        if($order_count=='1' && $order['pay_status']=='0'){
            $money=userMoney($uid);
            if($money){
                if($money['nowmoney_kt']>=$order['price']){
                    $data_zf['uid'] = $user['uid'];
                    $data_zf['sr_uid'] = 0;
                    $data_zf['money'] = $order['price'];
                    $data_zf['phone'] = $user['phone'];
                    $data_zf['type_cz'] = 2;
                    $data_zf['number'] = $order['num'];
                    $data_zf['check'] = 0;
                    $data_zf['realmoney'] = 0;
                    $data_zf['status'] = 1;
                    $data_zf['addtime'] = time();
                    $data_zf['endtime'] = time();
                    $data_zf['describe'] = '您于' . date("Y/m/d H:i:s") . '使用余额购买<em style="color: red;">'.$order['num'].'</em>个仓位，消费<em style="color: red;">￥'.$order['price'].'元</em>';
                    $ad_af = Db::name('getmoney')->insert($data_zf);
                    if($ad_af){
                        $order_pay=$this->way_order($order['order_sn'],'yuepay');
                        if($order_pay===true){
                            $messs1='恭喜您余额支付成功,订单号为:'.$order['order_sn'];
                            sendordermessage($uid,$order['id'],$messs1);
                            return json(['status' => 100, 'data' => '', 'msg' => '支付成功']);
                        }else{
                            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，订单状态修改失败。123456']);
                        }
                    }else{
                        return json(['status' => 400, 'data' => '', 'msg' => '支付失败，订单状态修改失败。']);
                    }

                }else{
                    return json(['status' => 400, 'data' => '', 'msg' => '用户可资金不足，可用其它方式支付']);
                }

            }else{
                return json(['status' => 400, 'data' => '', 'msg' => '支付失败，用户信息有误。']);
            }
        }else{
            return json(['status' => 400, 'data' => '', 'msg' => '支付失败，订单信息有误。']);
        }
    }

    /*
     *
     * 仓位订单
     * */
    function way_order($ordersn,$pay_type){
        $order=Db::name('waybill_order')->where('order_sn',$ordersn)->find();
        if($order['pay_status']=='1'){
            echo 'success';
            exit ();
        }

        if($pay_type=='wxpay'){
            $payname='微信';
        }elseif($pay_type=='yinlian'){
            $payname='银联';
        }elseif($pay_type=='alpay'){
            $payname='支付宝';
        }elseif($pay_type=='yuepay'){
            $payname='余额支付';
        }else{
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),"未知支付方式");
            return false;
        }

        $dd['status']=1;
        $dd['pay_status']=1;
        $dd['pay_type']=$pay_type;
        $dd['pay_time']=time();
        $up=Db::name('waybill_order')->where('order_sn',$ordersn)->update($dd);//更改状态信息
        if($up){
            Db::name('user')->where('uid',$order['uid'])->setInc('ticket_num',$order['num']);

            $member=Db::name('user')->where('uid',$order['uid'])->find();
            $user_info=$member;
            unset($user_info['password']);
            unset($user_info['openid']);
            Session::set('nowuser',$user_info);
            return true;
        }else{
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),$payname);
            return false;
        }
    }


    /*
     *
     * 商品订单
     * */
    function ret_order($ordersn,$pay_type){
        $order=Db::name('order')->where('order_sn',$ordersn)->find();
        if($order['pay_status']=='1'){
            echo 'success';
            exit ();
        }
        $payname='';
        if($pay_type=='wxpay'){
            $payname='微信支付';
            $data_log['name']='微信扫码支付';
            $data_log['remark']='微信扫码支付';
            $data_log['status']='微信扫码支付';
        }elseif($pay_type=='yinlian'){
            $payname='银联支付';
            $data_log['name']='银联支付';
            $data_log['remark']='银联支付';
            $data_log['status']='银联支付';
        }elseif($pay_type=='yuepay'){
            $payname='余额支付';
            $data_log['name']='余额支付';
            $data_log['remark']='余额支付';
            $data_log['status']='余额支付';
        }elseif($pay_type=='alpay'){
            $payname='支付宝支付';
            $data_log['name']='支付宝扫码支付';
            $data_log['remark']='支付宝扫码支付';
            $data_log['status']='支付宝扫码支付';
        }else{
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),"未知支付方式");
            return false;
        }

        $order_id=$order['id'];
        $dd['status']=1;
        $dd['pay_status']=1;
        $dd['pay_type']=$payname;
        $dd['pay_time']=time();
        $up=Db::name('order')->where('order_sn',$ordersn)->update($dd);//更改状态信息
        if($up){

            /*操作日志*/
            $data_log['o_id']=$order_id;
            $data_log['a_id']=0;
            $data_log['u_id']=$order['uid'];

            $data_log['addtime']=time();
            Db::name('order_log')->insert($data_log);

            $order_goods=Db::name('orderinfo')->where('order_id',$order_id)->field('gid,norm_id,num')->select();
            $goods_list=array();
            foreach ($order_goods as $k => $val) {
                $goodsinfo=Db::name('goods')->where('goods_id',$val['gid'])->field('goods_id,goods_number,goods_salnum')->find();
                if($goodsinfo){
                    //减库存总库存
                    Db::name('goods')->where('goods_id',$goodsinfo['goods_id'])->setDec('goods_number',$val['num']);
                    //加销量总销量
                    Db::name('goods')->where('goods_id',$goodsinfo['goods_id'])->setInc('goods_salnum',$val['num']);

                    if($goodsinfo['norm_id']){
                        //减库存规格库存
                        Db::name('goods_normprice')->where('id',$val['norm_id'])->setDec('num',$val['num']);
                        //加销量规格量
                        Db::name('goods_normprice')->where('id',$val['norm_id'])->setInc('salenum',$val['num']);
                    }

                    //库存判断
                    $info=Db::name('goods')->where('goods_id',$val['gid'])->field('goods_number,goods_salnum')->find();
                    if ($info['goods_number']<=0){
                        //库存小于0下架
                        $is_on_sale['is_on_sale']=0;
                        Db::name('goods')->where('goods_id',$goodsinfo['goods_id'])->update($is_on_sale);
                    }
                }
            }

            return true;
        }else{
            $t['addtime']=time();
            $t['msg']='error';
            pay_log_z(var_export($_POST,true),$payname);
            return false;
        }
    }

    function write_file_l($filepath,$filename, $data)
    {
        if (!file_exists($filepath)) {
            mkdir($filepath, 0777);
        }
        if (is_writable($filepath)) {
            file_put_contents($filepath . $filename, $data, FILE_APPEND);
        } else {
            chmod($filepath, 0777);
            file_put_contents($filepath . $filename, $data, FILE_APPEND);
        }
    }
}