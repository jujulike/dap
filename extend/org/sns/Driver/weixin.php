<?php

/**
 * 微信登陆Api
 *
 * @author Coeus <r.anerg@gmail.com>
 */

namespace org\sns\Driver;

class weixin extends \org\sns\OAuth
{

    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $AuthorizeURL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    /**
    扫码登陆接口
     ***/
    protected $AuthSmBase  =' https://open.weixin.qq.com/connect/qrconnect';
    /**
     * 获取Access Token的api接口
     * @var type String
     */
    protected $AccessTokenURL = 'https://api.weixin.qq.com/sns/oauth2/access_token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://api.weixin.qq.com/sns/';

    /**
     * 请求Authorize访问地址
     */
    public function getAuthorizeURL()
    {
        setcookie('A_S', $this->timestamp, $this->timestamp + 600, '/');
        $this->initConfig();
        //Oauth 标准参数
        $params = [
            'appid'         => $this->config['app_id'],
            'redirect_uri'  => $this->config['callback'],
            'response_type' => $this->config['response_type'],
            'scope'         => $this->config['scope'],
            'state'         => $this->timestamp,
        ];
        return $this->AuthorizeURL . '?' . http_build_query($params) . '#wechat_redirect';
    }
    /**
     * 扫码请求访问地址
     */
    public function getSaoMaURL()
    {
        setcookie('A_S', $this->timestamp, $this->timestamp + 600, '/');
        $this->initConfig();
        //Oauth 标准参数
        $params = [
            'appid'         => $this->config['app_id'],
            'redirect_uri'  => $this->config['callback'],
            'response_type' => $this->config['response_type'],
            'scope'         => $this->config['scope'],
            'state'         => $this->timestamp,
        ];
        return $this->AuthSmBase . '?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 获取中转代理地址
     */
    public function getProxyURL($proxy_url)
    {
        setcookie('A_S', $this->timestamp, $this->timestamp + 600, '/');
        $this->initConfig();
        //Oauth 标准参数
        $params = [
            'appid'         => $this->config['app_id'],
            'response_type' => $this->config['response_type'],
            'scope'         => $this->config['scope'],
            'state'         => $this->timestamp,
            'return_uri'    => $this->config['callback'],
        ];
        return $proxy_url . '?' . http_build_query($params);
    }

    /**
     * 默认的AccessToken请求参数
     * @return type
     */
    protected function _params($code = null)
    {
        $params = [
            'appid'      => $this->config['app_id'],
            'secret'     => $this->config['app_secret'],
            'grant_type' => $this->config['grant_type'],
            'code'       => is_null($code) ? $_GET['code'] : $code,
        ];
        return $params;
    }

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    微博API
     * @param  string $param  调用API的额外参数
     * @return json
     */
    public function call($api, $param = '')
    {
        /* 微信调用公共参数 */
        $params = [
            'access_token' => $this->token['access_token'],
            'openid'       => $this->openid(),
            'lang'         => 'zh_CN',
        ];

        $client   = new \GuzzleHttp\Client();
        $response = $client->request('GET', $this->url($api), ['form_params' => $this->param($params, $param)]);
        $data     = $response->getBody()->getContents();

        return json_decode($data, true);
    }

    /**
     * 解析access_token方法请求后的返回值
     * @param string $result 获取access_token的方法的返回值
     */
    protected function parseToken($result)
    {
        $data = json_decode($result, true);
        if (isset($data['access_token']) && isset($data['expires_in']) && isset($data['openid'])) {
            return $data;
        } else {
            throw new \Exception("获取微信 ACCESS_TOKEN 出错：{$result}");
        }
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid()
    {
        $data = $this->token;
        if (isset($data['openid'])) {
            return $data['openid'];
        } else {
            throw new \Exception('没有获取到微信用户ID！');
        }
    }

    /**
     * 获取授权用户的用户信息
     */
    public function userinfo()
    {
        $rsp = $this->call('userinfo');
        if (!$rsp || (isset($rsp['errcode']) && $rsp['errcode'] != 0)) {
            throw new \Exception('接口访问失败！' . $rsp['errmsg']);
        } else {
            $userinfo = [
                'openid'  => $this->token['openid'],
                'unionid' => isset($this->token['unionid']) ? $this->token['unionid'] : '',
                'channel' => 'weixin',
                'nick'    => $rsp['nickname'],
                'gender'  => $this->getGender($rsp['sex']),
                'avatar'  => $rsp['headimgurl'],
            ];
            return $userinfo;
        }
    }

    public function userinfoRaw()
    {
        $rsp = $this->call('userinfo');
        if (!$rsp || (isset($rsp['errcode']) && $rsp['errcode'] != 0)) {
            throw new \Exception('接口访问失败！' . $rsp['errmsg']);
        } else {
            return $rsp;
        }
    }

    private function getGender($gender)
    {
        $return = null;
        switch ($gender) {
            case 1:
                $return = 'm';
                break;
            case 2:
                $return = 'f';
                break;
            default:
                $return = 'n';
        }
        return $return;
    }

}
