<?php
namespace app\base\controller;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Session;
class Auth extends Base
{
    protected $loginType;   //存储登陆信息类型 session  cache   redis
    protected $uid;
    protected $member_info;
    //这里面放你的所需要的缩写
    public $uid_list = [
        'Node'=>'ND'//用户权限节点
    ];
    /**
     * 构造函数 开启checkLoginGlobal
     * power by HK
    **/
    public function initialize()
    {
        parent::initialize();
        $this->loginType=Config::get('login_type') ? Config::get('login_type') : "session";
        $this->checkLoginGlobal();
    }
    /**
     * 检测是否登陆
     * Power by HK
     * return bool
    **/
    public function checkLoginGlobal()
    {
        $check_success = false;
        switch ($this->loginType){
            case 1:
            case "session":
                $this->uid = Session::get('uid','Global');
                $this->member_info = Session::get('member_info','Global');
                if ($this->uid && $this->member_info){
                    $check_success = true;
                }
                break;
            case 2:
            case "cache":
                $ssesstion_id_check = Cookie::get('session_id');
                $this->uid = Cache::get("uid_{$ssesstion_id_check}");
                $this->member_info = Cache::get("member_info_{$ssesstion_id_check}");
                if ($this->uid && $this->member_info){
                    $check_success = true;
                }
                //刷新 缓存有效期
                Cache::set("uid_{$ssesstion_id_check}",$this->uid);
                Cache::set("member_info_{$ssesstion_id_check}",$this->member_info);
                break;
            case 3:
            case "redis":

                break;
        }
        return $check_success;
    }
    /**
     * 设置全局登录
     * Power by HK
     * return bool
     */
    public function setLoginGlobal($member_info = [], $login_code = 0)
    {
        $set_success = false ;
        if ($member_info) {
            switch ($this->loginType) {
                case 1:
                case "session":
                    Session::set('member_info', $member_info, 'Global');
                    Session::set('uid', $member_info['uid'], 'Global');
                    if ((Session::has("uid", "Global"))) {
                        $set_success = true;
                    }
                    break;
                case 2:
                case "cache":
                    $session_id = $this->create_uid("SN");
                    Cookie::set("session_id", $session_id);
                    Cache::set("member_info_$session_id", $member_info);
                    Cache::set("uid_$session_id", $member_info['uid']);
                    $session_id_check = Cookie::get("session_id");
                    if ((Cache::get("uid_{$session_id_check}"))) {
                        $set_success = true;
                    }
                    break;
                case 3:case "redis":


                break;
            }
        }
        if (!$set_success) return false;
        //保存登录记录
        $this->saveLoginInfo($member_info['uid'],$login_code);

        return true;
    }
    public function getLoginGlobal(){
        if($this->checkLoginGlobal()){
            return ['uid'=>$this->uid,'member_info'=>$this->member_info];
        }
        return false;
    }
    /**
     * 全局退出
     * Power by HK
     * @return bool
     */
    protected function logoutGlobal(){
        switch ($this->loginType) {
            case 1:
            case "session":
                Session::delete('uid', 'Global');
                Session::delete('member_info', 'Global');
                break;
            case 2:
            case "cache":
                $session_id_check = Cookie::get("session_id");
                Cache::rm("uid_{$session_id_check}");
                Cache::rm("member_info_{$session_id_check}");
                Cookie::delete("session_id");
                break;
            case 3:
            case "redis":


            break;
        }
        $this->member_info = null;
        $this->uid = null;
        return true;
    }
    /**
     * 创建个性GUID
     * 就是一个签名
     * Power by HK
     * @param string $base_code
     * @return string
     */
    public function create_uid($base_code = '')
    {
        if (empty($base_code)) {
            $base_name = basename(str_replace('\\', '/', get_called_class()), '.php');
            $uid_list = ModelCode::$uid_list;
            $base_code = isset($uid_list[$base_name]) ? $uid_list[$base_name] : 'PAY';
        }
        $uid = $base_code . strtoupper(uniqid()) . $this->builderRand(6);
        return $uid;
    }
    /**
     * 创建随机数
     * Power by HK
     * @param int $num  随机数位数
     * @return string
     */
    public function builderRand($num=8){
        return substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, $num);
    }

}