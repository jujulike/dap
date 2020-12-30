<?php
/*
 * @Descripttion:
 * @version:
 * @Author: zfc
 * @Date: 2020-11-19 11:31:05
 * @LastEditors: zfc
 * @LastEditTime: 2020-12-08 17:00:19
 */
/*
 * 此文件用于验证短信服务API接口，供开发时参考
 * 执行验证前请确保文件为utf-8编码，并替换相应参数为您自己的信息，并取消相关调用的注释
 * 建议验证前先执行Test.php验证PHP环境
 *
 * 2017/11/30
 */

namespace alisms;

 /**
  * 发送短信
  */
 class SendSms
 {
     public function ali_SendCode($phone, $code)
     {
         $params = [];
         // *** 需用户填写部分 ***
         // fixme 必填：是否启用https
         $security = false;
         // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
         $accessKeyId = 'LTAI4G2rmaReAju7aqC2bSah';
         $accessKeySecret = 'SZElVEoIctldBvsH66FnYOBymkgvTe';
         // fixme 必填: 短信接收号码
         $params['PhoneNumbers'] = $phone;
         // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
         $params['SignName'] = '楚留香网络科技有限公司'; //依日三餐
         // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://7dysms.console.aliyun.com/dysms.htm#/develop/template
         $params['TemplateCode'] = 'SMS_205311255'; //登录确认验证码  SMS_206120153//重置密码  SMS_205886986//商城注册验证码
         // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
         $params['TemplateParam'] = [
        'code' => $code,
    ];
         // fixme 可选: 设置发送短信流水号
         $params['OutId'] = '12345';
         // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
         $params['SmsUpExtendCode'] = '1234567';
         // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
         if (!empty($params['TemplateParam']) && is_array($params['TemplateParam'])) {
             $params['TemplateParam'] = json_encode($params['TemplateParam'], JSON_UNESCAPED_UNICODE);
         }
         // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
         $helper = new SignatureHelper();
         // 此处可能会抛出异常，注意catch
         $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        'dysmsapi.aliyuncs.com',
        array_merge($params, [
            'RegionId' => 'cn-hangzhou',
            'Action' => 'SendSms',
            'Version' => '2017-05-25',
        ]),
        $security
    );

         return $content;
     }

     //重置密码
     public function ali_SendPswCode($phone, $code)
     {
         $params = [];
         // *** 需用户填写部分 ***
         // fixme 必填：是否启用https
         $security = false;
         // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
         $accessKeyId = 'LTAI4G2rmaReAju7aqC2bSah';
         $accessKeySecret = 'SZElVEoIctldBvsH66FnYOBymkgvTe';
         // fixme 必填: 短信接收号码
         $params['PhoneNumbers'] = $phone;
         // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
         $params['SignName'] = '依日三餐'; //依日三餐
         // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://7dysms.console.aliyun.com/dysms.htm#/develop/template
         $params['TemplateCode'] = 'SMS_206120153'; //登录确认验证码  SMS_206120153//重置密码  SMS_205886986//商城注册验证码
         // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
         $params['TemplateParam'] = [
        'code' => $code,
    ];
         // fixme 可选: 设置发送短信流水号
         $params['OutId'] = '12345';
         // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
         $params['SmsUpExtendCode'] = '1234567';
         // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
         if (!empty($params['TemplateParam']) && is_array($params['TemplateParam'])) {
             $params['TemplateParam'] = json_encode($params['TemplateParam'], JSON_UNESCAPED_UNICODE);
         }
         // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
         $helper = new SignatureHelper();
         // 此处可能会抛出异常，注意catch
         $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        'dysmsapi.aliyuncs.com',
        array_merge($params, [
            'RegionId' => 'cn-hangzhou',
            'Action' => 'SendSms',
            'Version' => '2017-05-25',
        ]),
        $security
    );

         return $content;
     }

     //注册密码
     public function SendCode($phone, $code)
     {
         $params = [];
         // *** 需用户填写部分 ***
         // fixme 必填：是否启用https
         $security = false;
         // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
         $accessKeyId = 'LTAI4G2rmaReAju7aqC2bSah';
         $accessKeySecret = 'SZElVEoIctldBvsH66FnYOBymkgvTe';
         // fixme 必填: 短信接收号码
         $params['PhoneNumbers'] = $phone;
         // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
         $params['SignName'] = '依日三餐'; //依日三餐
         // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://7dysms.console.aliyun.com/dysms.htm#/develop/template
         $params['TemplateCode'] = 'SMS_205886986'; //登录确认验证码  SMS_206120153//重置密码  SMS_205886986//商城注册验证码
         // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
         $params['TemplateParam'] = [
        'code' => $code,
    ];
         // fixme 可选: 设置发送短信流水号
         $params['OutId'] = '12345';
         // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
         $params['SmsUpExtendCode'] = '1234567';
         // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
         if (!empty($params['TemplateParam']) && is_array($params['TemplateParam'])) {
             $params['TemplateParam'] = json_encode($params['TemplateParam'], JSON_UNESCAPED_UNICODE);
         }
         // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
         $helper = new SignatureHelper();
         // 此处可能会抛出异常，注意catch
         $content = $helper->request(
        $accessKeyId,
        $accessKeySecret,
        'dysmsapi.aliyuncs.com',
        array_merge($params, [
            'RegionId' => 'cn-hangzhou',
            'Action' => 'SendSms',
            'Version' => '2017-05-25',
        ]),
        $security
    );

         return $content;
     }
 }
