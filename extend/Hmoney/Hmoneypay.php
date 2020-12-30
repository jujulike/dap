<?php

namespace Hmoney;

class Hmoneypay
{
    // date_default_timezone_set('prc'); //php环境默认时差与北京时间相差8小时,设置北京时间
    //下单返回
    public function getPay($out_trade_no, $subject, $money, $info)
    {
        $url = 'https://pay.heemoney.com/v1/ApplyPay'; //请求地址
        //公共参数
        $method = 'heemoney.pay.applypay'; //具体业务接口名称
        $version = '1.0'; //版本号

        $app_id = 'hyp201109105890000030515F20FB8A4'; //应用ID，
        $mch_uid = '1058902127209'; //	商户统一编号//
        $key = 'CB3E5159EC0D4CF997AADF46'; //密钥

        $charset = 'UTF-8'; //编码格式
        $timestamp = date('YmdHis'); //发送请求的时间
        $biz_content = ''; //请求参数集合，Json格式，长度不限，具体参数见如下业务参数
        $sign_type = 'MD5'; //商户生成签名字符串所使用的签名算法类型
        $sign = ''; //商户请求参数的签名串

        //业务参数
        $out_trade_no = $out_trade_no; //商户系统内部订单号，要求64个字符内、且在同一个商户号下唯一
        $subject = $subject; //订单标题
        $total_fee = $money; //订单总金额，单位为分
        $channel_type = 'WX_APPLET'; //通道类型(微信支付)
        $client_ip = '127.0.0.1'; //用户端ip
        $attach = ''; //附加数据，在查询API和支付通知中原样返回，可作为自定义参数使用格式：{“key1”:”value1”,”key2”:”value2”,…}
        $pay_option =['open_id' => 'o1NRJ4ysAXljb2p4A7Jl8C9EZPF4', 'app_id' => 'wx1b1b4a148bacc3b1']; //支付参数信息
        $pay_option = json_encode($pay_option);
        $meta_option = ''; //商户定制信息，格式：{“key1”:”value1”,”key2”:”value2”,…}
        $notify_url = 'http://'; //异步通知的地址
        $return_url = 'http://'; //同步通知地址
        $wx_js_code = '053Vi3Ga12wsaA0H9uFa163s9b2Vi3Go'; //微信id
        $biz_content = [
            'out_trade_no' => $out_trade_no,
            'subject' => $subject,
            'total_fee' => $total_fee,
            'client_ip' => $client_ip,
            'pay_option' => $pay_option,
             'notify_url' => $notify_url,
              'return_url' => $return_url,
               'channel_type' => $channel_type,
               'wx_js_code' => $wx_js_code,
            ];
        $biz_content = json_encode($biz_content);
        $biz_content = str_replace('\\/', '/', $biz_content);
        $sign_str = 'app_id='.$app_id.'&biz_content='.$biz_content.'&charset='.$charset.'&mch_uid='.$mch_uid.'&method='.$method.'&sign_type='.$sign_type.'&timestamp='.$timestamp.'&version='.$version.'&key='.$key;
        $signString = strtoupper(md5($sign_str));

        $a = ['method' => $method, 'version' => $version, 'app_id' => $app_id, 'mch_uid' => $mch_uid, 'charset' => $charset, 'sign_type' => $sign_type, 'timestamp' => $timestamp];
        $a['biz_content'] = $biz_content;
        $a['sign'] = $signString;

        $resultoo = $this->http($url, $a, 1);

        return $resultoo;
    }

    //订单查询
    public function querryOrder($hy_bill_no, $out_trade_no)
    {
        $url = 'https://pay.heemoney.com/v1/payquery'; //请求地址
        //公共参数
        $method = 'heemoney.pay.query'; //具体业务接口名称
        $version = '1.0'; //版本号

        $app_id = 'hyp201109105890000030515F20FB8A4'; //应用ID，
        $mch_uid = '1058902127209'; //	商户统一编号//
        $key = 'CB3E5159EC0D4CF997AADF46'; //密钥

        $charset = 'UTF-8'; //编码格式
        $timestamp = date('YmdHis'); //发送请求的时间
        $biz_content = ''; //请求参数集合，Json格式，长度不限，具体参数见如下业务参数
        $sign_type = 'MD5'; //商户生成签名字符串所使用的签名算法类型
        $sign = ''; //商户请求参数的签名串

        $hy_bill_no = $hy_bill_no;
        $out_trade_no = $out_trade_no;
        $biz_content = ['hy_bill_no' => $hy_bill_no, 'out_trade_no' => $out_trade_no];
        $biz_content = json_encode($biz_content);
        $biz_content = str_replace('\\/', '/', $biz_content);

        $sign_str = 'app_id='.$app_id.'&biz_content='.$biz_content.'&charset='.$charset.'&mch_uid='.$mch_uid.'&method='.$method.'&sign_type='.$sign_type.'&timestamp='.$timestamp.'&version='.$version.'&key='.$key;
        $signString = strtoupper(md5($sign_str));

        $a = ['method' => $method, 'version' => $version, 'app_id' => $app_id, 'mch_uid' => $mch_uid, 'charset' => $charset, 'sign_type' => $sign_type, 'timestamp' => $timestamp];
        $a['biz_content'] = $biz_content;
        $a['sign'] = $signString;

        $resultoo = $this->http($url, $a, 1);

        return $resultoo;
    }

    //退款提交
    public function refund($out_trade_no, $refundOrder, $total_fee, $refund_fee)
    {
        $url = 'https://pay.heemoney.com/v1/refund'; //请求地址
        //公共参数
        $method = 'heemoney.pay.refund'; //具体业务接口名称
        $version = '1.0'; //版本号

        $app_id = 'hyp201109105890000030515F20FB8A4'; //应用ID，
        $mch_uid = '1058902127209'; //	商户统一编号//
        $key = 'CB3E5159EC0D4CF997AADF46'; //密钥

        $charset = 'UTF-8'; //编码格式
        $timestamp = date('YmdHis'); //发送请求的时间
        $biz_content = ''; //请求参数集合，Json格式，长度不限，具体参数见如下业务参数
        $sign_type = 'MD5'; //商户生成签名字符串所使用的签名算法类型
        $sign = ''; //商户请求参数的签名串

        $out_trade_no = $out_trade_no; //订单号
        $out_refund_no = $refundOrder; //退款单号
        $total_fee = $total_fee; //订单总价
        $refund_fee = $refund_fee; //退款金额

        $biz_content = [
        'out_trade_no' => $out_trade_no,
        'out_refund_no' => $out_refund_no,
        'total_fee' => $total_fee,
        'refund_fee' => $refund_fee,
        ];

        $biz_content = json_encode($biz_content);
        $biz_content = str_replace('\\/', '/', $biz_content);

        $sign_str = 'app_id='.$app_id.'&biz_content='.$biz_content.'&charset='.$charset.'&mch_uid='.$mch_uid.'&method='.$method.'&sign_type='.$sign_type.'&timestamp='.$timestamp.'&version='.$version.'&key='.$key;
        $signString = strtoupper(md5($sign_str));

        $a = ['method' => $method, 'version' => $version, 'app_id' => $app_id, 'mch_uid' => $mch_uid, 'charset' => $charset, 'sign_type' => $sign_type, 'timestamp' => $timestamp];
        $a['biz_content'] = $biz_content;
        $a['sign'] = $signString;

        $resultoo = $this->http($url, $a, 1);

        return $resultoo;
    }

    //分账接口
    public function ledgerAccount($hy_bill_no, $out_trade_no, $data = [])
    {
        $url = 'https://pay.heemoney.com/v1/GuaranteeAllot'; //请求地址
        //公共参数
        $method = 'heemoney.guaranteeallot.submit'; //具体业务接口名称
        $version = '1.0'; //版本号

        $app_id = 'hyp201109105890000030515F20FB8A4'; //应用ID，
        $mch_uid = '1058902127209'; //	商户统一编号//
        $key = 'CB3E5159EC0D4CF997AADF46'; //密钥

        $charset = 'UTF-8'; //编码格式
        $timestamp = date('YmdHis'); //发送请求的时间
        $biz_content = ''; //请求参数集合，Json格式，长度不限，具体参数见如下业务参数
        $sign_type = 'MD5'; //商户生成签名字符串所使用的签名算法类型
        $sign = ''; //商户请求参数的签名串

        $hy_bill_no = $hy_bill_no; //订单号
        $out_trade_no = $out_trade_no; //单号
        $allot_data = json_encode($data);
        $allot_data = $allot_data; //订单分配

        // "allot_data":"[{\"login_account\":\"B\",\"allot_amt_fen\":\"100\"}，{\"login_account\":\"C\",\"allot_amt_fen\":\"200\"}]"

        $biz_content = [
        'hy_bill_no' => $hy_bill_no,
        //'out_trade_no' => $out_trade_no,
        'allot_data' => $allot_data,
        ];

        $biz_content = json_encode($biz_content);
        $biz_content = str_replace('\\/', '/', $biz_content);

        $sign_str = 'app_id='.$app_id.'&biz_content='.$biz_content.'&charset='.$charset.'&mch_uid='.$mch_uid.'&method='.$method.'&sign_type='.$sign_type.'&timestamp='.$timestamp.'&version='.$version.'&key='.$key;
        $signString = strtoupper(md5($sign_str));

        $a = ['method' => $method, 'version' => $version, 'app_id' => $app_id, 'mch_uid' => $mch_uid, 'charset' => $charset, 'sign_type' => $sign_type, 'timestamp' => $timestamp];
        $a['biz_content'] = $biz_content;
        $a['sign'] = $signString;
        
        $resultoo = $this->http($url, $a, 1);

        return $resultoo;
    }

    public function http($url, $data = null, $json = false)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        if (!empty($data)) {
            if ($json && is_array($data)) {
                $data = json_encode($data);
                $data = str_replace('\\/', '/', $data); //数组转json后http://问题处理
            //  echo "请求数据=".$data;
            // echo "<br>";
            }
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if ($json) { //发送JSON数据
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:'.strlen($data), ]
            );
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        $errorno = curl_errno($curl);

        if ($errorno) {
            return ['errorno' => false, 'errmsg' => $errorno];
        }
        curl_close($curl);
        // echo  "返回信息=".$res;
        // echo "<br>";
        return $res;
    }
}
