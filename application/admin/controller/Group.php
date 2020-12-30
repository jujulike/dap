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
class Group extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
        //15天自动收货

        // $orders = Db::name('shop_order')->where(['sent' => 2, 'pay_status' => 1, 'expresstime' => ['lt', time()]])->column('id');

        if ($ordes) {
            foreach ($orders as $v) {
                Db::name('shop_order')->where(['id' => $v])->update(['sent' => 3]);
            }
        }
    }

    //订单列表
    public function order()
    {
        $map = ['is_delete' => 0];
        $istypes = intval(input('istypes'));
        if ($istypes) {
            $map['istypes'] = $istypes - 1;
        }
        $istime = input('istime/d');
        if ($istime) {
            $map['addtime'] = ['egt', strtotime(date('Y-m-d'))];
        }
        $time = input('start_time');
        if ($time) {
            $time = explode(' - ', $time);
            $map['addtime'] = [['egt', strtotime($time[0])], ['elt', strtotime($time[1])]];
        }
        $types = intval(input('types'));
        if ($types == 1) {
            $map['pay_status'] = 0;
            $map['sent'] = ['>', 0];
        } elseif ($types == 2) {
            $map['pay_status'] = 1;
            $map['sent'] = 1;
        } elseif ($types == 3) {
            $map['sent'] = 2;
            $map['pay_status'] = 1;
        } elseif ($types == 4) {
            $map['pay_status'] = 1;
            $map['sent'] = ['gt', 2];
        } elseif ($types == 5) {
            $map['sent'] = -2;
        } elseif ($types == 6) {
            //$map['pay_status'] = 0;
        }
        $btns = $_GET['btns'];
        //搜索
        $order_sn = input('order_sn');
        if ($order_sn) {
            $map['order_sn'] = ['like', '%'.$order_sn.'%'];
        }
        $order_code = input('order_code');
        if ($order_code) {
            $map['order_code'] = ['like', '%'.$order_code.'%'];
        }

        $is_dispatch = intval(input('is_dispatch'));
        if ($is_dispatch) {
            $map['is_dispatch'] = $is_dispatch - 1;
            $this->assign('is_dispatch', $is_dispatch);
        }
        $pay_type = intval(input('pay_type'));
        if ($pay_type) {
            $map['pay_type'] = $pay_type - 1;
            $this->assign('pay_type', $pay_type);
        }
        $keyword = input('keyword');
        if ($keyword) {
            $map['address'] = ['like', '%'.$keyword.'%'];
        }

        if ($btns == 'explode' || $btns == 'explodegood') {
            $lists = Db::name('order')->where($map)->select();
        } else {
            $list = $this->lists('order', $map, 'id desc');
            foreach ($list as $k => $v) {
                $list[$k]['statuscode'] = $this->getstatuscode($v['pay_status'], $v['sent']);
                // $name = Db::name('dispatch')->where('id', $v['dispatchid'])->value('name');
                // $v['zdname'] = $name;
            }
        }

        if ($btns == 'explode') {
            //$lists=Db::name("shop_order")->where($map)->select();
            if (!$lists) {
                $this->error('数据为空');
            }
            /* 输入到CSV文件 */
            $html = "\xEF\xBB\xBF";
            /* 输出表头 */
            $filter = [
                'order_sn' => '订单号',
                'price' => '金额',
                'is_dispatch' => '提货方式',
                'pay_type' => '支付方式',
                'istypes' => '订单类型',
                'sent' => '订单状态',
                'address' => '用户信息',
                //'goods'=>'商品信息',
                'addtime' => '下单时间',
                'dispatchname' => '自提点',
            ];
            foreach ($filter as $key => $title) {
                $html .= $title."\t,";
            }
            $html .= "\n";
            foreach ($lists as $k => $v) {
                $dispatchname = '';
                if ($v['dispatchid']) {
                    $dispatchname = Db::name('dispatch')->where('id', $v['dispatchid'])->value('name');
                }
                foreach ($filter as $key => $title) {
                    if ($key == 'is_dispatch') {
                        if ($v['is_dispatch']) {
                            $html .= '上门自提'."\t, ";
                        } else {
                            $html .= '在线发货'."\t, ";
                        }
                    } elseif ($key == 'dispatchname') {
                        $html .= $dispatchname."\t, ";
                    } elseif ($key == 'pay_type') {
                        if ($v['pay_type']) {
                            $html .= '微信支付'."\t, ";
                        } else {
                            $html .= '余额支付'."\t, ";
                        }
                    } elseif ($key == 'sent') {
                        if ($v['sent'] > 2) {
                            $html .= '已完成'."\t, ";
                        } elseif ($v['sent'] == 2) {
                            $html .= '待收货'."\t, ";
                        } elseif ($v['sent'] == 1) {
                            $html .= '待发货'."\t, ";
                        } elseif ($v['sent'] == -1) {
                            if ($v['isrefund']) {
                                $html .= '已退款'."\t, ";
                            } else {
                                $html .= '退款中'."\t, ";
                            }
                        } elseif ($v['sent'] == 0 && $v['is_cancle'] == 0) {
                            $html .= '未付款'."\t, ";
                        } else {
                            $html .= '已取消'."\t, ";
                        }
                    } elseif ($key == 'addtime') {
                        $html .= date('Y-m-d', $v['addtime'])."\t, ";
                    } else {
                        $html .= $v[$key]."\t, ";
                    }
                }
                $html .= "\n";
            }
            /* 输出CSV文件 */
            header('Content-type:text/csv');
            header('Content-Disposition:attachment; filename=商城订单.csv');
            echo $html;
            exit();
        }
        if ($btns == 'explodegood') {
            if (!$lists) {
                $this->error('数据为空');
            }
            $allgood = [];
            foreach ($lists as $v) {
                $goods = Db::name('shop_order_good')->where(['order_id' => $v['id']])->select();
                $addr = explode('|', $v['address']);
                $dispatchname = '';
                if ($v['dispatchid']) {
                    $dispatchname = Db::name('dispatch')->where('id', $v['dispatchid'])->value('name');
                }
                foreach ($goods as $k => $g) {
                    if ($v['istypes'] == 1) {
                        $title = Db::name('Miaosha_good')->where(['id' => $g['goodid']])->value('title');
                        $item['goodname'] = $title;
                        $item['goodprice'] = $v['goodsprice'];
                    } else {
                        $good = Db::name('Good')->where(['id' => $g['goodid']])->field('title,price')->find();
                        $item['goodname'] = $good['title'];
                        $item['goodprice'] = $good['price'];
                    }
                    $item['is_dispatch'] = $v['is_dispatch'];
                    $item['order_sn'] = $v['order_sn'];
                    $item['expresssn'] = $v['expresssn'];
                    $item['num'] = $g['num'];
                    if ($g['optionid']) {
                        $option = Db::name('good_option')->where(['id' => $g['optionid']])->find();
                        $item['goodprice'] = $option['price'];
                        $item['optionname'] = $option['title'];
                    } else {
                        $item['optionname'] = '';
                    }
                    $item['addtime'] = date('Y-m-d', $v['addtime']);
                    $item['username'] = $addr[0];
                    $item['mobile'] = $addr[1];

                    $item['address'] = $addr[2];
                    $item['price'] = $v['price'];
                    $item['sent'] = $v['sent'];
                    $item['isrefund'] = $v['isrefund'];
                    $item['pay_type'] = $v['pay_type'];
                    $item['totalprice'] = $g['num'] * $item['goodprice'];
                    $item['detail'] = str_replace(',', '，', $v['detail']);
                    $item['dispatchname'] = $dispatchname;
                    $item['dispat_time'] = $v['dispat_time'];
                    $allgood[] = $item;
                }
            }
            /* 输入到CSV文件 */
            $html = "\xEF\xBB\xBF";
            /* 输出表头 */
            $filter = [
                'order_sn' => '订单号',
                'pay_type' => '支付方式',
                'addtime' => '日期',
                'goodname' => '产品',
                'optionname' => '规格',
                'num' => '数量',
                'goodprice' => '单价',
                'totalprice' => '销售总价',
                'price' => '订单总价',
                'username' => '订货人',
                'address' => '配送地址',
                'mobile' => '联系电话',
                'is_dispatch' => '配送方式',
                'expresssn' => '物流单号',
                'sent' => '订单状态',
                'detail' => '订单备注',
                'dispatchname' => '自提点',
                'dispat_time' => '自提时间',
            ];
            foreach ($filter as $key => $title) {
                $html .= $title."\t,";
            }
            $html .= "\n";
            foreach ($allgood as $k => $v) {
                foreach ($filter as $key => $title) {
                    if ($key == 'is_dispatch') {
                        if ($v['is_dispatch']) {
                            $html .= '上门自提'."\t, ";
                        } else {
                            $html .= '在线发货'."\t, ";
                        }
                    } elseif ($key == 'pay_type') {
                        if ($v['sent']) {
                            if ($v['pay_type']) {
                                $html .= '微信支付'."\t, ";
                            } else {
                                $html .= '余额支付'."\t, ";
                            }
                        } else {
                            $html .= '未支付'."\t, ";
                        }
                    } elseif ($key == 'sent') {
                        if ($v['sent'] > 2) {
                            $html .= '已完成'."\t, ";
                        } elseif ($v['sent'] == 2) {
                            $html .= '待收货'."\t, ";
                        } elseif ($v['sent'] == 1) {
                            $html .= '待发货'."\t, ";
                        } elseif ($v['sent'] == -1) {
                            if ($v['isrefund']) {
                                $html .= '已退款'."\t, ";
                            } else {
                                $html .= '退款中'."\t, ";
                            }
                        } elseif ($v['sent'] == 0 && $v['is_cancle'] == 0) {
                            $html .= '未付款'."\t, ";
                        } else {
                            $html .= '已取消'."\t, ";
                        }
                    } else {
                        $html .= $v[$key]."\t, ";
                    }
                }
                $html .= "\n";
            }
            /* 输出CSV文件 */
            if ($pay_status) {
                $name = $this->order_status[$pay_status];
            } else {
                $name = '全部';
            }
            header('Content-type:text/csv');
            header('Content-Disposition:attachment; filename='.$name.'商品列表.csv');
            echo $html;
            exit();
        }
        $this->assign('list', $list);
        $this->assign('types', $types);
        $this->assign('istypes', $istypes);
        $this->assign('meta_title', '商城订单');

        return $this->fetch();
    }

    //订单详情
    public function order_info()
    {
        $did = intval(input('id'));
        if (request()->isPost()) {
            $did = intval(input('id'));
            $order = Db::name('order')->where(['id' => $did])->field('order_sn,pay_status')->find();
            if (!$order) {
                $this->error('订单信息错误');
            }
            if (!empty($_POST['express']) && empty($_POST['expresssn'])) {
                message('请输入快递单号！');
            }
            $data['sent'] = 2;
            // $data['express'] = $_POST['express'];
            //$data['expresssn'] = $_POST['expresssn'];
            action_log('update_shop_order', 'shop_order', $did, UID); //记录行为
            //$data['expresstime'] = strtotime('+15 day');

            Db::name('order')->where(['id' => $did])->update($data);
            $s2['sent'] = 3;
            $s2['express'] = $_POST['express'];
            $s2['expresssn'] = $_POST['expresssn'];
            $s2['expresstime'] = time();
            Db::name('shop_order')->where(['order_sn' => $order['order_sn']])->update($s2);
            $this->success('发货操作成功！');
        } else {
            $order_info = Db::name('order')->where(['id' => $did])->find();
            $order_info['statusword'] = $this->getstatuscode($order_info['paystatus'], $order_info['sent']);
            $order_info['shoplist'] = $this->getorders($order_info['order_sn'], 'sid,totalprice');

            if ($order_info['malltype'] == 3) {
                $order_info['malltypecode'] = '自提';
            }
            if ($order_info['malltype'] == 2) {
                $order_info['malltypecode'] = '快小驴物流';
            }
            if ($order_info['malltype'] == 1) {
                $order_info['malltypecode'] = '快小驴专车';
            }
            if ($order_info['malltype'] != '3') {
                $bigorder = Db::name('order')->where('order_sn', $order_info['order_sn'])->find();
                $addr = explode('|', $bigorder['address']);
                $order_info['name'] = $addr[0];
                $order_info['mobile'] = substr_replace($addr[1], '****', 3, 4);
                $order_info['address'] = $addr[2];
            } else {
                $order_info['name'] = $order_info['username'];
                $order_info['mobile'] = substr_replace($order_info['userphone'], '****', 3, 4);
                $order_info['address'] = '';
            }
            $order['orderaddtime'] = date('Y-m-s h:i', $order_info['addtime']);
            $order_info['goodnum'] = Db::name('shop_order_good')->where('order_sn', $order_info['order_sn'])->sum('num');
            $addr = explode('|', $order_info['address']);
            $this->assign('addr', $addr);

            $this->assign('item', $order_info);
            $orderaction = Db::name('order_actions')->where('order_sn', $order_info['order_sn'])->select();
            $this->assign('orderaction', $orderaction);
            $this->assign('meta_title', '订单详情');

            return $this->fetch();
        }
    }

    public function order_sent()
    {
        $op = input('op');
        $did = intval(input('id'));
        $order = Db::name('shop_order')->where(['id' => $did])->field('goodsprice,user_id,is_sent')->find();
        if ($op == 'close') {
            $data['sent'] = 1;
        } elseif ($op == 'finish') {
            if ($order['sent'] == 4) {
                $this->error('交易已完成');
            }

            $data['sent'] = 4;
            $data['updata_time'] = time();
        } elseif ($op == 'since') {
            if ($order['sent'] == 3) {
                $this->error('交易已完成');
            }
            $data['sent'] = 3;
        //积分获得
        } elseif ($op == 'refund') {
            $data['sent'] = $order['is_sent'];
        }
        action_log('update_shop_order', 'shop_order', $did, UID); //记录行为
        Db::name('user')->where(['uid' => $order['user_id']])->update(['is_remind' => 1]);
        Db::name('shop_order')->where(['id' => $did])->update($data);
        $this->success('操作成功');
    }

    public function order_sent_pay()
    {
        $did = intval(input('id'));
        $order = Db::name('order')->where(['id' => $did])->field('order_sn,pay_status')->find();
        if (!$order) {
            $this->error('订单信息错误');
        }
        if ($order['pay_status'] == 1) {
            $this->error('订单已支付');
        }
        $data['pay_status'] = 1;
        $data['pay_time'] = time();
        action_log('update_shop_order', 'shop_order', $did, UID); //记录行为

        Db::name('order')->where(['id' => $did])->update($data);
        $s2['paystatus'] = 1;
        $s2['pay_time'] = time();
        Db::name('shop_order')->where(['order_sn' => $order['order_sn']])->update($s2);
        $this->success('操作成功');
    }

    public function tkorder()
    {
        $ordersn = input('order_sn');
        if ($ordersn) {
            $whe['order_sn'] = ['like', '%'.$ordersn.'%'];
        }
        $goodname = input('goodname');
        if ($goodname) {
            $whe['goodname'] = ['like', '%'.$goodname.'%'];
        }
        $type = input('types');
        if (!$type) {
            $type = 0;
        }

        switch ($type) {
            case 1:
                $whe['refundstatus'] = '1'; //待处理
                $whe['is_refundok'] = '0';
                break;
            case 3:
                $whe['refundstatus'] = '2'; //商家同意
                $whe['is_refundok'] = '0'; //已完成
                break;
            case 4:
                $whe['is_refundok'] = '1'; //已完成
                break;
            case 2:
                $whe['refundstatus'] = '3'; //申请失败
                break;
            default:
                $whe['refundtype'] = ['>', 0];
        }

        $order = $this->lists('shop_order_good', $whe, 'id desc');

        foreach ($order as $key => $val) {
            $order[$key]['refund'] = '待处理';
            if ($val['refundstatus'] == '1') {
                $order[$key]['refund'] = '待处理';
            }
            if ($val['refundstatus'] == '2') {
                $order[$key]['refund'] = '商家已同意';
            }
            if ($val['refundstatus'] == '3') {
                $order[$key]['refund'] = '商家已拒绝';
            }
            if ($val['is_refundok'] == '1') {
                $order[$key]['refund'] = '已完成';
            }

            $order[$key]['opname'] = $order['optionid'] ? $order['optionid'] : $order['goodname'];

            $order[$key]['shop'] = $this->getordershop($val['sid']);
            $order[$key]['totalmoney'] = $val['num'] * $val['goodprice'];
        }

        $this->assign('list', $order);
        $this->assign('types', $type);
        $this->assign('istypes', $istypes);
        $this->assign('meta_title', '退款订单列表');

        return $this->fetch();
    }

    public function tkorderinfo()
    {
        $orderid = input('id');

        $whe['id'] = $orderid;
        $order = Db::name('shop_order_good')->where($whe)->find();
        if (!$order) {
            $this->error('信息错误');
        }
        $order['refund'] = '待处理';
        if ($order['refundstatus'] == '1') {
            $order['refund'] = '待处理';
        }
        if ($order['refundstatus'] == '2') {
            $order['refund'] = '已同意';
        }
        if ($order['refundstatus'] == '3') {
            $order['refund'] = '已拒绝';
        }
        if ($order['is_refundok'] == '1') {
            $order['refund'] = '已完成';
        }

        $order['opname'] = $order['optionid'] ? $order['optionid'] : $order['goodname'];
        //$order['goodpic'] = Db::name('good')->where('id', $order['gid'])->value('photo_x');
        $order['totalmoney'] = $order['num'] * $order['goodprice'];
        $bigorder = Db::name('order')->where('order_sn', $order['order_sn'])->find();
        $order['shop'] = $this->getordershop($order['sid']);
        if ($bigorder['malltype'] == 1) {
            $order['malltypecode'] = '自提';
        }
        if ($bigorder['malltype'] == 2) {
            $order['malltypecode'] = '快小驴物流';
        }
        if ($bigorder['malltype'] == 3) {
            $order['malltypecode'] = '快小驴专车';
        }
        if ($bigorder['malltype'] != '1') {
            $addr = explode('|', $bigorder['address']);
            $order['name'] = $addr[0];
            $order['mobile'] = substr_replace($addr[1], '****', 3, 4);
            $order['address'] = $addr[2];
        } else {
            $order['name'] = $bigorder['username'];
            $order['mobile'] = substr_replace($bigorder['userphone'], '****', 3, 4);
            $order['address'] = '';
        }
        $order['orderaddtime'] = date('Y-m-s h:i', $bigorder['addtime']);
        $this->assign('list', $order);

        $this->assign('meta_title', '退款订单详情');

        return $this->fetch();
    }

    //退款
    public function orderrefund()
    {
        $did = input('id');
        $order = Db::name('shop_order')->where(['id' => $did])->find();
        if ($order['pay_status'] && $order['sent'] == -1 && empty($order['isrefund'])) {
            if ($order['is_sent'] == 1) {
                $price = $order['price'];
            } else {
                $price = $order['price'] - $order['mailprice'];
            }

            if ($order['pay_type']) {
                $wxPay = new \wxpay\WxpayRefund();
                $result = $wxPay->doRefund($order['price'], $price, $order['trade_no'], $order['order_sn'], $order['remark']);

                if ($result['result_code'] == 'SUCCESS') {
                    $row['isrefund'] = 1;
                } elseif ($result['result_code'] == 'FAIL') {
                    $this->error($result['err_code_des']);
                }
            }
            Db::name('user')->where(['uid' => $order['user_id']])->update(['is_remind' => 1]);
            $res = Db::name('shop_order')->where(['id' => $did])->update($row);
            if ($res) {
                $this->success('退款成功');
            } else {
                $this->error('退款失败');
            }
        } else {
            $this->error('您已经操作过退款，请不要重复操作');
        }
    }

    //删除订单
    public function order_del()
    {
        $did = input('id');
        $res = Db::name('shop_order')->where(['id' => $did])->count();
        if ($res) {
            action_log('del_shop_order', 'shop_order', $did, UID); //记录行为
            Db::name('shop_order')->where(['id' => $did])->update(['is_delete' => 1]);
            $this->success('删除成功');
        } else {
            $this->error('订单存在已经删除');
        }
    }

    //修改价格
    public function upprice()
    {
        $did = input('id');
        $couponprice = Db::name('order')->where('id', $did)->value('couprice');
        if (!$couponprice) {
            $couponprice = 0;
        }
        $goodsprice = floatval($_POST['goodsprice']);
        $mailprice = floatval($_POST['mailprice']);
        $price = $goodsprice + $mailprice - $couponprice;
        if ($price < 0 || $mailprice < 0 || $goodsprice < 0) {
            $this->error('价格不能小于0');
        }
        Db::name('order')->where('id', $did)->update(['totalprice' => $price, 'goodsprice' => $goodsprice, 'mallprice' => $mailprice]);
        $this->success('修改价格成功');
    }

    // 结算列表
    public function money()
    {
        $ordersn = input('order_sn');
        if ($ordersn) {
            $whe['order_sn'] = ['like', '%'.$ordersn.'%'];
        }
        $goodname = input('goodname');
        if ($goodname) {
            $w['goodname'] = ['like', '%'.$goodname.'%'];
        }
        $whe['sent'] = ['>', 3];
        $whe['stime'] = ['>', 0];

        $order = $this->lists('shop_order', $whe, 'id desc');

        foreach ($order as $k => $v) {
            $order[$k]['shop'] = Db::name('supuser')->where('sid', $v['sid'])->field('sid,headimgurl,shopname')->find();
        }
        $this->assign('meta_title', '订单结算');
        $this->assign('list', $order);

        return $this->fetch();
    }

    public function moneyinfo()
    {
        $ordersn = input('ordersn');

        $whe['order_sn'] = $ordersn;

        $order_info = Db::name('order')->where($whe)->find();

        if ($order_info['malltype'] == 3) {
            $order_info['malltypecode'] = '自提';
        }
        if ($order_info['malltype'] == 2) {
            $order_info['malltypecode'] = '快小驴物流';
        }
        if ($order_info['malltype'] == 1) {
            $order_info['malltypecode'] = '快小驴专车';
        }
        if ($order_info['malltype'] != '3') {
            $bigorder = Db::name('order')->where('order_sn', $order_info['order_sn'])->find();
            $addr = explode('|', $bigorder['address']);
            $order_info['name'] = $addr[0];
            $order_info['mobile'] = substr_replace($addr[1], '****', 3, 4);
            $order_info['address'] = $addr[2];
        } else {
            $order_info['name'] = $order_info['username'];
            $order_info['mobile'] = substr_replace($order_info['userphone'], '****', 3, 4);
            $order_info['address'] = '';
        }
        $order_info['orderaddtime'] = date('Y-m-s h:i', $order_info['addtime']);
        $order_info['orderpaytime'] = date('Y-m-s h:i', $order_info['pay_time']);
        $shop = Db::name('shop_order')->where($whe)->select();
        foreach ($shop as $k => $v) {
            $shop[$k]['shopname'] = Db::name('supuser')->where('sid', $v['sid'])->value('shopname');
            $shop[$k]['latemoney'] = 0;
            if (!($v['upmoney'] > 0.01)) {
                $shop[$k]['latemoney'] = $v['totalprice'] - ($v['totalprice'] * 0.0048);
            }
        }

        $this->assign('list', $order_info);
        $this->assign('shop', $shop);
        $this->assign('meta_title', '订单结算详情');

        return $this->fetch();
    }

    public function jsinfo()
    {
        $ordersn = input('orderid');
        $whe['id'] = $ordersn;
        $order = Db::name('shop_order')->where($whe)->find();
        $data = $this->getordergood($order['order_sn'], $order['sid']);

        $this->assign('shop', $data);
        $this->assign('meta_title', '商户结算详情');

        return $this->fetch();
    }

    public function getstatuscode($pay, $sent)
    {
        switch ($sent) {
            case '1'://待处理
                $statusword = '待发货';
                break;
            case '2'://待发货
                $statusword = '待收货';
                break;
            case '3'://待收货
                $statusword = '待评价';
                break;
            case '4'://待
                $statusword = '交易完成';
                break;
            case '5'://已完成
                $statusword = '交易完成';
                break;
            case '-2'://已作废
                $statusword = '交易关闭';
                break;
            default:
                $where['id'] = ['>', 0];
        }
        if (!$pay && ($sent != -2)) {
            $statusword = '待付款';
        }

        return $statusword;
    }

    public function getorders($ordersn, $fild)
    {
        $shoporder = db::name('shop_order')->where('order_sn', $ordersn)->field($fild)->select();

        foreach ($shoporder as $k => $v) {
            $shop = $this->getordershop($v['sid']);
            $shopgood = $this->getordergood($ordersn, $v['sid']);
            $shoporder[$k]['shop'] = $shop;
            $shoporder[$k]['shopgood'] = $shopgood;

            $shoporder[$k]['shopgoodsum'] = Db::name('shop_order')->where('order_sn', $ordersn)->where('sid', $v['sid'])->value('totalprice');
        }

        return $shoporder;
    }

    public function getordershop($sid)
    {
        $supuser = Db::name('supuser')->where(['sid' => $sid])
            ->field('sid,headimgurl,shopname')->find();
        if ($supuser['headimgurl']) {
            $supuser['headimgurl'] = __DATAURL__.$supuser['headimgurl'];
        }

        return $supuser;
    }

    public function getordergood($ordersn, $sid)
    {
        $data = Db::name('shop_order_good')->where('order_sn', $ordersn)->where('sid', $sid)->select();
        $count = Db::name('shop_order_good')->where('order_sn', $ordersn)->where('sid', $sid)->sum('num');
        foreach ($data as $k => $v) {
            //$option = Db::name('good_option')->where('id', $v['optionid'])->value('title');
            $data[$k]['opname'] = $v['optionid'] ? $v['optionid'] : $v['goodname'];
            $data[$k]['goodpic'] = Db::name('good')->where('id', $v['gid'])->value('photo_x');
        }
        $t['goods'] = $data;
        $t['goodscount'] = $count;

        return $t;
    }
}
