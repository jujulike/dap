<?php
namespace alidayu;

use alidayu\top\TopClient;
use alidayu\top\request\AlibabaAliqinFcSmsNumSendRequest;

class AliSendCode{
    //发送验证码
    public function ali_SendCode($phone,$code){
            $c = new TopClient;
            $c->appkey = '23893751';
            $c->secretKey = '2ea1ee2665a7ca7275800cd108847d25';
            $req = new AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("匹匹鸟优购");
            $req->setSmsParam("{\"code\":\"$code\",\"product\":\"匹匹鸟\"}");
            $req->setRecNum($phone);
            $req->setSmsTemplateCode("SMS_70240399");
            $resp = $c->execute($req);
            return $resp;
    }
     public function ali_SendCode2($phone,$code){
            $c = new TopClient;
            $c->appkey = 'LTAI4G2rmaReAju7aqC2bSah';
            $c->secretKey = 'SZElVEoIctldBvsH66FnYOBymkgvTe';
            $req = new AlibabaAliqinFcSmsNumSendRequest;
            $req->setSmsType("normal");
            $req->setSmsFreeSignName("楚留香网络科技有限公司");
            $req->setSmsParam("{\"code\":\"$code\",\"product\":\"楚留香\"}");
            $req->setRecNum($phone);
            $req->setSmsTemplateCode("SMS_205311255");
            $resp = $c->execute($req);
            return $resp;
    }
    // 商家审核通过
    public function ali_SendCode_shop($phone){
        $c = new TopClient;
        $c->appkey = '23893751';  //AccessKeyId
        $c->secretKey = '2ea1ee2665a7ca7275800cd108847d25';  //AccessKeySecret
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("匹匹鸟优购");//商家签名
        $req->setRecNum($phone);//手机号
        $req->setSmsTemplateCode("SMS_76240021");//短信模板代码
        $resp = $c->execute($req);
        return $resp;
    }
    // 商家审核未通过
    public function ali_SendCode_shops($phone){
        $c = new TopClient;
        $c->appkey = '23893751';
        $c->secretKey = '2ea1ee2665a7ca7275800cd108847d25';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("匹匹鸟优购");
        $req->setRecNum($phone);
        $req->setSmsTemplateCode("SMS_76220014");
        $resp = $c->execute($req);
        return $resp;
    }
    // 兑换提现成功
    public function ali_SendCode_convert($phone){
        $c = new TopClient;
        $c->appkey = '23893751';
        $c->secretKey = '2ea1ee2665a7ca7275800cd108847d25';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("匹匹鸟优购");
        $req->setRecNum($phone);
        $req->setSmsTemplateCode("SMS_76360007");
        $resp = $c->execute($req);
        return $resp;
    }
	// 兑换提现申请提示
    public function ali_SendCode_newconvert($phone){
        $c = new TopClient;
        $c->appkey = '23893751';
        $c->secretKey = '2ea1ee2665a7ca7275800cd108847d25';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("匹匹鸟优购");
        $req->setRecNum($phone);
        $req->setSmsTemplateCode("SMS_90960032");
        $resp = $c->execute($req);
        return $resp;
    }
	// 通知企业审核
    public function ali_SendCode_shopreal($phone){
        $c = new TopClient;
        $c->appkey = '23893751';
        $c->secretKey = '2ea1ee2665a7ca7275800cd108847d25';
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName("匹匹鸟优购");
        $req->setRecNum($phone);
        $req->setSmsTemplateCode("SMS_90930062");
        $resp = $c->execute($req);
        return $resp;
    }
	
}