<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2018/10/8
 * Time: 11:24
 */
namespace org\qqlogin;
use think\Session;

class Oauth
{
    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    /*protected $recorder;*/
    public $urlUtils;
  /*  protected $error;*/
    public $state;
    public $appid = "101504691";
    public $appkey="409247baf10ec1124b148dfbb861c79d" ;
    public $callback = "http://loosent.host36.cqhansa.net/home/publics/qqcallback";
    public $scope = "get_user_info";

  function __construct(){
        /*$this->recorder = new Recorder();*/
        $this->urlUtils = new URL();
       /* $this->error = new ErrorCase();*/
    }

    public function qq_login(){
        $appid = $this->appid;
        $callback = $this->callback;
        $scope = $this->scope;

        //-------生成唯一随机串防CSRF攻击
        $state = md5(uniqid(rand(), TRUE));
		
        Session::set('state',$state);
	
       /* $this->recorder->write('state',$state);*/

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );

        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
        return $login_url;
        $this->redirect();
       // header("Location:$login_url");
    }

    public function qq_callback(){
        $state = Session::get('state');
		$tt=$_GET['state'];
		/**var_dump($tt);
		echo '<br>';
var_dump($state);**/
        //--------验证state防止CSRF攻击
        if((!$state) || ($_GET['state'] != $state)){
            exit("30001");
        }

        //-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->appid,
            "redirect_uri" => urlencode($this->callback),
            "client_secret" => $this->appkey,
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);

        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                exit();
                $this->error->showError($msg->error, $msg->error_description);
            }
        }

        $params = array();
        parse_str($response, $params);

        //$this->recorder->write("access_token", $params["access_token"]);
        Session::set('access_token',$params["access_token"]);
        return $params["access_token"];

    }

    public function get_openid(){

        //-------请求参数列表
        $keysArr = array(
            "access_token" =>Session::get('access_token'),
            /* $this->recorder->read("access_token")*/
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);

        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
            exit();
            $this->error->showError($user->error, $user->error_description);
        }

        //------记录openid
        //$this->recorder->write("openid", $user->openid);
        Session::set('openid',$user->openid);
        return $user->openid;

    }
}