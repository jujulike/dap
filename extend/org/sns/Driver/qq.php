<?php

/**
 * QQ登陆Api
 *
 * @author Coeus <r.anerg@gmail.com>
 */

namespace org\sns\Driver;

class qq extends \org\sns\OAuth
{

    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $AuthorizeURL = 'https://graph.qq.com/oauth2.0/authorize';

    /**
     * 获取Access Token的api接口
     * @var type String
     */
    protected $AccessTokenURL = 'https://graph.qq.com/oauth2.0/token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://graph.qq.com/';

    /**
     * 请求Authorize访问地址
     */
    public function getAuthorizeURL()
    {
        setcookie('A_S', $this->timestamp, $this->timestamp + 600, '/');
        $this->initConfig();
        //Oauth 标准参数
        $params = [
            'response_type' => $this->config['response_type'],
            'client_id'     => $this->config['app_id'],
            'redirect_uri'  => $this->config['callback'],
            'state'         => $this->timestamp,
            'scope'         => $this->config['scope'],
            'display'       => $this->display,
        ];
        return $this->AuthorizeURL . '?' . http_build_query($params);
    }

    /**
     * 组装接口调用参数 并调用接口
     * @param  string $api    微博API
     * @param  string $param  调用API的额外参数
     * @return json
     */
    public function call($api, $param = '')
    {
        /* 腾讯QQ调用公共参数 */
        $params = [
            'oauth_consumer_key' => $this->config['app_id'],
            'access_token'       => $this->token['access_token'],
            'openid'             => $this->openid(),
            'format'             => 'json',
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
        parse_str($result, $data);
        if (isset($data['access_token']) && isset($data['expires_in'])) {
            $this->token    = $data;
            $data['openid'] = $this->openid();
            return $data;
        } else {
            throw new \Exception("获取腾讯QQ ACCESS_TOKEN 出错：{$result}");
        }
    }

    /**
     * 获取当前授权应用的openid
     * @return string
     */
    public function openid()
    {
        $data = $this->tokentoken;
        if (isset($data['openid'])) {
            return $data['openid'];
        } elseif ($data['access_token']) {
            $client   = new \GuzzleHttp\Client();
            $response = $client->request('GET', $this->url('oauth2.0/me'), ['form_params' => ['access_token' => $data['access_token']]]);
            $data     = $response->getBody()->getContents();
            $data     = json_decode(trim(substr($data, 9), " );\n"), true);
            if (isset($data['openid'])) {
                return $data['openid'];
            } else {
                throw new \Exception("获取用户openid出错：{$data['error_description']}");
            }
        } else {
            throw new \Exception('没有获取到openid！');
        }
    }

    /**
     * 获取授权用户的用户信息
     */
    public function userinfo()
    {
        $rsp = $this->call('user/get_user_info');
        if (!$rsp || $rsp['ret'] != 0) {
            throw new \Exception('接口访问失败！' . $rsp['msg']);
        } else {
            $userinfo = [
                'openid'  => $this->openid(),
                'channel' => 'qq',
                'nick'    => $rsp['nickname'],
                'gender'  => $rsp['gender'] == "男" ? 'm' : 'f',
                'avatar'  => $rsp['figureurl_qq_2'] ? $rsp['figureurl_qq_2'] : $rsp['figureurl_qq_1'],
            ];
            return $userinfo;
        }
    }

    public function userinfoRaw()
    {
        $rsp = $this->call('user/get_user_info');
        if (!$rsp || $rsp['ret'] != 0) {
            throw new \Exception('接口访问失败！' . $rsp['msg']);
        } else {
            return $rsp;
        }
    }

}
