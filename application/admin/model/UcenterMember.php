<?php
// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------
namespace app\common\model;

use think\Model;

/**
 * 会员模型
 */
class UcenterMember extends Model
{
    // 定义时间戳字段名
    protected $createTime = 'reg_time';
    protected $updateTime = 'update_time';
    /* 用户模型自动完成 */
    //新增及更新
    //  protected $auto = ['password','update_time'];
    // 新增
    protected $insert = ['password', 'status' => 1, 'reg_ip', 'reg_time'];
    // 更新
    protected $update = ['password', 'update_time'];
    // password属性修改器
    protected function setPasswordAttr($value, $data)
    {
        return think_ucenter_md5($data['password'], UC_AUTH_KEY);
    }
    protected function setRegIpAttr($value, $data)
    {
        return get_client_ip(1);
    }

    /**
     * 检测用户名是不是被禁止注册
     * @param  string $username 用户名
     * @return boolean          ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMember($username)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测邮箱是不是被禁止注册
     * @param  string $email 邮箱
     * @return boolean       ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyEmail($email)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 检测手机是不是被禁止注册
     * @param  string $mobile 手机
     * @return boolean        ture - 未禁用，false - 禁止注册
     */
    protected function checkDenyMobile($mobile)
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 根据配置指定用户状态
     * @return integer 用户状态
     */
    protected function getStatus()
    {
        return true; //TODO: 暂不限制，下一个版本完善
    }

    /**
     * 注册一个新用户
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email    用户邮箱
     * @param  string $mobile   用户手机号码
     * @param  stting $scene  验证场景  admin 后台 user为用户注册
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function register($username, $password, $email, $mobile, $scene = '')
    {
        //'email'    => $email,
        $data = array(
            'username' => $username,
            'password' => $password,
            'mobile'   => $mobile,
            'status'   => 1,
        );

        //验证手机
        if (empty($data['mobile'])) {
            unset($data['mobile']);
        }

        // /* 规则验证 */
        if (empty($scene)) {
            $scene = true;
        }

        $validate = \think\Loader::validate('UcenterMember');
        if (!$validate->scene($scene)->check($data)) {

            return $validate->getError();
        }

        /* 添加用户 */
        if ($user_data = $this->create($data)) {
            $user_data = $user_data->toArray();
        }

        if ($user_data) {
            $uid = $user_data['uid'];
            return $uid ? $uid : 0; //0-未知错误，大于0-注册成功
        } else {
            return $this->getError();
        }
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1)
    {
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['uid'] = $username;
                break;
            default:
                return 0; //参数错误
        }

        /* 获取用户数据 */
        if ($user = $this->where($map)->find()) {
            $user = $user->toArray();
        }

        if (is_array($user) && $user['status']) {
            /* 验证用户密码 */
            if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                $this->updateLogin($user['uid']); //更新用户登录信息
                return $user['uid']; //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }

    /**
     * 获取用户信息
     * @param  string  $uid         用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false)
    {
        $map = array();
        if ($is_username) {
            //通过用户名获取
            $map['username'] = $uid;
        } else {
            $map['uid'] = $uid;
        }

        $user = \think\Db::name($this->name)->where($map)->field('uid,username,email,mobile,status')->find();
        if (is_array($user) && $user['status'] == 1) {
            //记录行为
            action_log('user_login', 'member', $uid, $uid);

            /* 登录用户 */
            $this->autoLogin($user);
            return true;
        } else {
            return -1; //用户不存在或被禁用
        }
    }
    private function autoLogin($user)
    {
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'username'        => $user['username'],
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }
    /**
     * 检测用户信息
     * @param  string  $field  用户名
     * @param  integer $type   用户名类型 1-用户名，2-用户邮箱，3-用户电话
     * @return integer         错误编号
     */
    public function checkField($field, $type = 1)
    {
        $data = array();
        switch ($type) {
            case 1:
                $data['username'] = $field;
                break;
            case 2:
                $data['email'] = $field;
                break;
            case 3:
                $data['mobile'] = $field;
                break;
            default:
                return 0; //参数错误
        }

        return $this->create($data) ? 1 : $this->getError();
    }

    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid)
    {
        $data = array(
            'uid'             => $uid,
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => time(),
            'last_login_ip'   => get_client_ip(1),
        );
        $this->where(array('uid' => $uid))->update($data);
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * Author: zfc000000
     */
    public function updateUserFields($uid, $password, $data)
    {
        if (empty($uid) || empty($password) || empty($data)) {
            $return['status'] = false;
            $return['info']   = '参数错误！';
            return $return;
        }

        //更新前检查用户密码
        if (!$this->verifyUser($uid, $password)) {
            $return['status'] = false;
            $return['info']   = '验证出错：密码不正确！';
            return $return;
        }
        //更新用户信息
        $this->allowField(true)->isUpdate($data, ['uid' => $uid])->save($data);
        $return['status'] = true;
        return $return;exit;
    }

    /**
     * 验证用户密码
     * @param int $uid 用户id
     * @param string $password_in 密码
     * @return true 验证成功，false 验证失败
     * Author: zfc000000
     */
    protected function verifyUser($uid, $password_in)
    {
        $password = $this->getFieldByUid($uid, 'password');
        if (think_ucenter_md5($password_in, UC_AUTH_KEY) === $password) {
            return true;
        }
        return false;
    }
    /**
     * 注销当前用户
     * @return void
     */
    public function logout()
    {
        session('user_auth', null);
        session('user_auth_sign', null);
    }
    public function getNickName($uid)
    {
        return $this->where(array('uid' => (int) $uid))->value('username');
    }

}
