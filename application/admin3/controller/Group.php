<?php
/**
 * Created by PhpStorm.
 * User: wm
 * Date: 2018/3/13
 * Time: 17:57
 */
namespace app\admin\controller;

use think\Db;

/**
 * 行为控制器
 * Author: zfc000000
 */

class Group extends Admin
{
    public function _initialize()
    {
        parent::_initialize();
        //15天自动收货
        /**$orders = Db::name("shopp_order")->where(['sent' => 2, 'pay_status' => 1, 'expresstime' => array('lt', time())])->column("id");
        if ($orders) {
            foreach ($orders as $v) {
                Db::name("shopp_order")->where(['id' => $v])->update(['sent' => 3]);
            }
        }**/
    }
    //订单列表
    public function order()
    {
        //$map     = array("is_delete" => 0);
        $istypes = intval(input('istypes'));
        if ($istypes) {
            $map["istypes"] = $istypes - 1;
        }
        $istime = input("istime/d");
        if ($istime) {
            $map["addtime"] = array("egt", strtotime(date("Y-m-d")));
        }
        $time = input('start_time');
        if ($time) {
            $time           = explode(" - ", $time);
            $map["addtime"] = array(array("egt", strtotime($time[0])), array("elt", strtotime($time[1])));
        }
        $types = intval(input('types'));
        if ($types == 1) {
            $map['pay_status'] = 0;
            $map['is_cancle']  = 0;
        } elseif ($types == 2) {
            $map['sent'] = 1;
        } elseif ($types == 3) {
            $map['sent'] = 2;
        } elseif ($types == 4) {
            $map['sent'] = array("gt", 2);
        } elseif ($types == 5) {
            $map['sent'] = -1;
        } elseif ($types == 6) {
            $map['pay_status'] = 0;
            $map['is_cancle']  = 1;
        }
        $btns = $_GET["btns"];
        //搜索
        $order_sn = input('order_sn');
        if ($order_sn) {
            $map['order_sn'] = array('like', '%' . $order_sn . '%');
        }
        $order_code = input('order_code');
        if ($order_code) {
            $map['order_code'] = array('like', '%' . $order_code . '%');
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
            $map['address'] = array('like', '%' . $keyword . '%');
        }
        if ($btns == 'explode' || $btns == 'explodegood') {
            $lists = Db::name("order")->where($map)->select();

        } else {
            $list = $this->lists('order', $map, 'id desc');
            foreach ($list as &$v) {
                $name        = Db::name("dispatch")->where('id', $v['dispatchid'])->value('name');
                $v['zdname'] = $name;
            }
        }

        if ($btns == 'explode') {
            //$lists=Db::name("shopp_order")->where($map)->select();
            if (!$lists) {$this->error('数据为空');}
            /* 输入到CSV文件 */
            $html = "\xEF\xBB\xBF";
            /* 输出表头 */
            $filter = array(
                'order_sn'     => '订单号',
                'price'        => '金额',
                'is_dispatch'  => '提货方式',
                'pay_type'     => '支付方式',
                'istypes'      => '订单类型',
                'sent'         => '订单状态',
                'address'      => '用户信息',
                //'goods'=>'商品信息',
                'addtime'      => '下单时间',
                'dispatchname' => '自提点',
            );
            foreach ($filter as $key => $title) {
                $html .= $title . "\t,";
            }
            $html .= "\n";
            foreach ($lists as $k => $v) {
                $dispatchname = "";
                if ($v['dispatchid']) {
                    $dispatchname = Db::name("dispatch")->where('id', $v['dispatchid'])->value("name");
                }
                foreach ($filter as $key => $title) {
                    if ($key == 'is_dispatch') {
                        if ($v['is_dispatch']) {
                            $html .= '上门自提' . "\t, ";
                        } else {
                            $html .= '在线发货' . "\t, ";
                        }

                    } elseif ($key == 'dispatchname') {
                        $html .= $dispatchname . "\t, ";
                    } elseif ($key == 'pay_type') {
                        if ($v['pay_type']) {
                            $html .= '微信支付' . "\t, ";
                        } else {
                            $html .= '余额支付' . "\t, ";
                        }
                    } elseif ($key == 'istypes') {
                        if ($v['istypes'] == 1) {
                            $html .= '秒杀' . "\t, ";
                        } elseif ($v['istypes'] == 2) {
                            $html .= '拼团' . "\t, ";
                        } else {
                            $html .= '商城' . "\t, ";
                        }
                    } elseif ($key == 'sent') {
                        if ($v['sent'] > 2) {
                            $html .= '已完成' . "\t, ";
                        } elseif ($v['sent'] == 2) {
                            $html .= '待收货' . "\t, ";
                        } elseif ($v['sent'] == 1) {
                            $html .= '待发货' . "\t, ";
                        } elseif ($v['sent'] == -1) {
                            if ($v['isrefund']) {
                                $html .= '已退款' . "\t, ";
                            } else {
                                $html .= '退款中' . "\t, ";
                            }
                        } elseif ($v['sent'] == 0 && $v['is_cancle'] == 0) {
                            $html .= '未付款' . "\t, ";
                        } else {
                            $html .= '已取消' . "\t, ";
                        }
                    } elseif ($key == 'addtime') {
                        $html .= date("Y-m-d", $v['addtime']) . "\t, ";
                    } else {
                        $html .= $v[$key] . "\t, ";
                    }
                }
                $html .= "\n";
            }
            /* 输出CSV文件 */
            header("Content-type:text/csv");
            header("Content-Disposition:attachment; filename=商城订单.csv");
            echo $html;
            exit();
        }
        if ($btns == 'explodegood') {

            if (!$lists) {$this->error('数据为空');}
            $allgood = array();
            foreach ($lists as $v) {
                $goods        = Db::name("shopp_order_good")->where(['order_id' => $v['id']])->select();
                $addr         = explode("|", $v['address']);
                $dispatchname = "";
                if ($v['dispatchid']) {
                    $dispatchname = Db::name("dispatch")->where('id', $v['dispatchid'])->value("name");
                }
                foreach ($goods as $k => $g) {
                    if ($v['istypes'] == 1) {
                        $title             = Db::name("Miaosha_good")->where(["id" => $g['goodid']])->value('title');
                        $item['goodname']  = $title;
                        $item['goodprice'] = $v['goodsprice'];
                    } else {
                        $good              = Db::name("Good")->where(["id" => $g['goodid']])->field('title,price')->find();
                        $item['goodname']  = $good['title'];
                        $item['goodprice'] = $good['price'];
                    }
                    $item['is_dispatch'] = $v['is_dispatch'];
                    $item['order_sn']    = $v['order_sn'];
                    $item['expresssn']   = $v['expresssn'];
                    $item['num']         = $g['num'];
                    if ($g['optionid']) {
                        $option             = Db::name("good_option")->where(['id' => $g['optionid']])->find();
                        $item['goodprice']  = $option['price'];
                        $item['optionname'] = $option['title'];
                    } else {
                        $item['optionname'] = '';
                    }
                    $item['addtime']  = date("Y-m-d", $v['addtime']);
                    $item['username'] = $addr[0];
                    $item['mobile']   = $addr[1];

                    $item['address']      = $addr[2];
                    $item['price']        = $v['price'];
                    $item['sent']         = $v['sent'];
                    $item['isrefund']     = $v['isrefund'];
                    $item['pay_type']     = $v['pay_type'];
                    $item['totalprice']   = $g['num'] * $item['goodprice'];
                    $item['detail']       = str_replace(",", '，', $v['detail']);
                    $item['dispatchname'] = $dispatchname;
                    $item['dispat_time']  = $v['dispat_time'];
                    $allgood[]            = $item;
                }
            }
            /* 输入到CSV文件 */
            $html = "\xEF\xBB\xBF";
            /* 输出表头 */
            $filter = array(
                'order_sn'     => '订单号',
                'pay_type'     => '支付方式',
                'addtime'      => '日期',
                'goodname'     => '产品',
                'optionname'   => '规格',
                'num'          => '数量',
                'goodprice'    => '单价',
                'totalprice'   => '销售总价',
                'price'        => '订单总价',
                'username'     => '订货人',
                'address'      => '配送地址',
                'mobile'       => '联系电话',
                'is_dispatch'  => '配送方式',
                'expresssn'    => '物流单号',
                'sent'         => '订单状态',
                'detail'       => '订单备注',
                'dispatchname' => '自提点',
                'dispat_time'  => '自提时间',
            );
            foreach ($filter as $key => $title) {
                $html .= $title . "\t,";
            }
            $html .= "\n";
            foreach ($allgood as $k => $v) {
                foreach ($filter as $key => $title) {
                    if ($key == 'is_dispatch') {
                        if ($v['is_dispatch']) {
                            $html .= '上门自提' . "\t, ";
                        } else {
                            $html .= '在线发货' . "\t, ";
                        }
                    } elseif ($key == 'pay_type') {
                        if ($v['sent']) {
                            if ($v['pay_type']) {
                                $html .= '微信支付' . "\t, ";
                            } else {
                                $html .= '余额支付' . "\t, ";
                            }
                        } else {
                            $html .= '未支付' . "\t, ";
                        }

                    } elseif ($key == 'sent') {
                        if ($v['sent'] > 2) {
                            $html .= '已完成' . "\t, ";
                        } elseif ($v['sent'] == 2) {
                            $html .= '待收货' . "\t, ";
                        } elseif ($v['sent'] == 1) {
                            $html .= '待发货' . "\t, ";
                        } elseif ($v['sent'] == -1) {
                            if ($v['isrefund']) {
                                $html .= '已退款' . "\t, ";
                            } else {
                                $html .= '退款中' . "\t, ";
                            }
                        } elseif ($v['sent'] == 0 && $v['is_cancle'] == 0) {
                            $html .= '未付款' . "\t, ";
                        } else {
                            $html .= '已取消' . "\t, ";
                        }
                    } else {
                        $html .= $v[$key] . "\t, ";
                    }
                }
                $html .= "\n";
            }
            /* 输出CSV文件 */
            if ($pay_status) {
                $name = $this->order_status[$pay_status];
            } else {
                $name = "全部";
            }
            header("Content-type:text/csv");
            header("Content-Disposition:attachment; filename=" . $name . "商品列表.csv");
            echo $html;
            exit();
        }
        $this->assign('list', $list);
        $this->assign('types', $types);
        $this->assign('istypes', $istypes);
        $this->assign('meta_title', '商城订单');
        return $this->fetch();
    }

    public  function tkorder(){
        return $this->fetch();
    }
    //订单详情
    public function order_info()
    {
        $did = intval(input('id'));
        if (request()->isPost()) {
            if (!empty($_POST['express']) && empty($_POST['expresssn'])) {
                message('请输入快递单号！');
            }
            $data["sent"]      = 2;
            $data['express']   = $_POST['express'];
            $data['expresssn'] = $_POST['expresssn'];
            action_log('update_shopp_order', 'shopp_order', $did, UID); //记录行为
            $data['expresstime'] = strtotime('+15 day');

            Db::name('shopp_order')->where(['id' => $did])->update($data);
            $this->success("发货操作成功！");
        } else {
            $order_info = Db::name('shopp_order')->where(['id' => $did])->find();

            $addr = explode("|", $order_info['address']);
            $this->assign('addr', $addr);

            $dispatch = Db::name("dispatch")->where('id', $order_info['dispatchid'])->find();
            $goods    = Db::name("shopp_order_good")->where(['order_id' => $order_info['id']])->select();
            foreach ($goods as &$v) {
                if ($order_info['istypes'] == 1) {
                    $title      = Db::name("Miaosha_good")->where(array("id" => $v['goodid']))->value('title');
                    $price      = Db::name("ms_good")->where(array("id" => $order_info['msid']))->value('price');
                    $v['title'] = $title;
                    $v['price'] = $price;
                } else {
                    $gd         = Db::name("good")->where(['id' => $v['goodid']])->field("title,price")->find();
                    $v['title'] = $gd['title'];
                    $v['price'] = $gd['price'];
                }
                if ($v['optionid']) {
                    $option     = Db::name("good_option")->where(['id' => $v['optionid']])->find();
                    $v['price'] = $option['price'];
                    $v['name']  = $option['title'];
                }

            }
            $user = Db::name("user")->where("uid", $order_info['user_id'])->field('nickname,mobile,realname')->find();
            $this->assign('user', $user);
            $this->assign('goods', $goods);
            $this->assign('dispatch', $dispatch);
            $this->assign('item', $order_info);
            $this->assign('meta_title', '订单详情');
            return $this->fetch();
        }

    }

    public function order_sent()
    {
        $op    = input('op');
        $did   = intval(input('id'));
        $order = Db::name("shopp_order")->where(['id' => $did])->field("goodsprice,user_id,is_sent")->find();
        if ($op == "close") {
            $data['sent'] = 1;
        } elseif ($op == "finish") {
            if ($order['sent'] == 4) {
                $this->error('交易已完成');
            }
            //积分获得
            $web_score = Db::name("info")->where('id', 1)->value('score');
            $score     = ceil($order['goodsprice'] * $web_score);
            if ($score) {
                $rs = array('user_id' => $order['user_id'], 'note' => "购买商品", 'score' => $score, 'addtime' => time(), 'sent' => 1);
                Db::name("score_act")->insert($rs);
                Db::name("user")->where(['uid' => $order['user_id']])->setInc("totalscore", $score);
            }
            $data['sent']        = 4;
            $data['updata_time'] = time();
        } elseif ($op == "since") {
            if ($order['sent'] == 3) {
                $this->error('交易已完成');
            }
            $data['sent'] = 3;
            //积分获得
            $web_score = Db::name("info")->where('id', 1)->value('score');
            $score     = ceil($order['goodsprice'] * $web_score);
            if ($score) {
                $rs = array('user_id' => $order['user_id'], 'note' => "购买商品", 'score' => $score, 'addtime' => time(), 'sent' => 1);
                Db::name("score_act")->insert($rs);
                Db::name("user")->where(['uid' => $order['user_id']])->setInc("totalscore", $score);
            }
        } elseif ($op == "refund") {
            $data['sent'] = $order['is_sent'];
        }
        action_log('update_shopp_order', 'shopp_order', $did, UID); //记录行为
        Db::name("user")->where(['uid' => $order['user_id']])->update(['is_remind' => 1]);
        Db::name("shopp_order")->where(['id' => $did])->update($data);
        $this->success("操作成功");
    }
    //退款
    public function orderrefund()
    {
        $did   = input('id');
        $order = Db::name('shopp_order')->where(array('id' => $did))->find();
        if ($order['pay_status'] && $order['sent'] == -1 && empty($order['isrefund'])) {
            if ($order['is_sent'] == 1) {
                $price = $order['price'];
            } else {
                $price = $order['price'] - $order['mailprice'];
            }

            if ($order['pay_type']) {
                $wxPay  = new \wxpay\WxpayRefund();
                $result = $wxPay->doRefund($order['price'], $price, $order['trade_no'], $order['order_sn'], $order['remark']);

                if ($result['result_code'] == 'SUCCESS') {
                    $row['isrefund'] = 1;
                } elseif ($result['result_code'] == 'FAIL') {
                    $this->error($result['err_code_des']);
                }
            } else {
                $row['isrefund'] = 1;
                Db::name("user")->where(['uid' => $order['user_id']])->setInc("totalprice", $price);
                //余额记录
                $arr = array("user_id" => $order['user_id'], "remark" => '商城商品退款', 'price' => $price, 'sent' => 0, 'addtime' => time());
                Db::name("balance_act")->insert($arr);
            }
            Db::name("user")->where(['uid' => $order['user_id']])->update(['is_remind' => 1]);
            $res = Db::name("shopp_order")->where(['id' => $did])->update($row);
            if ($res) {
                //退库存
                if ($order['istypes'] == 1) {
                    Db::name("ms_good")->where('id', $order['msid'])->setInc("num");
                } else {
                    $good = Db::name("shopp_order_good")->where(['order_id' => $did])->select();
                    foreach ($good as $v) {
                        if ($v['optionid']) {
                            Db::name("good_option")->where('id', $v['optionid'])->setInc("total", $v['num']);
                        } else {
                            Db::name("good")->where('id', $v['goodid'])->setInc("total", $v['num']);
                        }
                    }
                }

                $this->success("退款成功");
            } else {
                $this->error("退款失败");
            }
        } else {
            $this->error("您已经操作过退款，请不要重复操作");
        }
    }
    //删除订单
    public function order_del()
    {
        $did = input('id');
        $res = Db::name('shopp_order')->where(array('id' => $did))->count();
        if ($res) {
            action_log('del_shopp_order', 'shopp_order', $did, UID); //记录行为
            Db::name('shopp_order')->where(array('id' => $did))->update(['is_delete' => 1]);
            $this->success("删除成功");
        } else {
            $this->error("订单存在已经删除");
        }
    }
    //修改价格
    public function upprice()
    {
        $did         = input('id');
        $couponprice = Db::name("shopp_order")->where("id", $did)->value("couponprice");
        $goodsprice  = floatval($_POST['goodsprice']);
        $mailprice   = floatval($_POST['mailprice']);
        $price       = $goodsprice + $mailprice - $couponprice;
        if ($price < 0 || $mailprice < 0 || $goodsprice < 0) {
            $this->error("价格不能小于0");
        }
        Db::name("shopp_order")->where("id", $did)->update(['price' => $price, 'goodsprice' => $goodsprice, 'mailprice' => $mailprice]);
        $this->success('修改价格成功');
    }
}
