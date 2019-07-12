<?php
namespace app\base\controller;
use think\facade\App;
use think\Container;
use think\Controller;
use think\facade\Request;
/**
 * 底层封装类
 * power by HK
 * DataTime 2019/7/4 15：09
**/
class Base extends Controller
{
    /**
     * 构造方法
     * @param Request $request Request对象
     * power by HK
     */
    public function __construct(App $app = null)
    {
        $this->app     = $app ?: Container::get('app');
        $this->request = $this->app['request'];
        $this->view    = $this->app['view'];

        // 控制器初始化
        $this->initialize();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                    $this->beforeAction($options) :
                    $this->beforeAction($method, $options);
            }
        }
    }
    /**
     * 权限类初始化
     * Power by HK
     */
    public function initialize()
    {
        parent::initialize(); // 初始化
//        $user_agent = $this->request->server('HTTP_USER_AGENT');
//        if (! strpos($user_agent, 'MicroMessenger') === false ) $this->isWechatBrowser = true;
//        //判断提交方式和是否微信浏览器
//        if ($this->request->method() == 'GET' && $this->isWechatBrowser === true){
//            //未登录 重新登录
//            if (!$this->checkAuth()&& !$this->no_login )  $this->wxoauth();
//            $this->isLogin=true;
//            //设置全局登录
//            $this->loginGlobal();
//            if(!$this->isReg){
//                if(!$this->checkUidMobile()) $this->redirect('user/user_blind.html');
//            }
//        }

    }
    /**
     * 返回数据及状态
     * @param   string  $code
     * @param   array   $data
     * @param   string  $msg
     * @return  array
     **/
    static public function showReturnCode($code = '',$data = [], $msg = ''){
        $return_data=[
            'code'  =>  '500',
            'msg'   =>  '未定义消息',
            'data'  =>  $code == 200 ? $data : []
        ];
        if(empty($code)) return $return_data;
        $return_data['code'] = $code;
        if(!empty($msg)){
            $return_data['msg'] = $msg;
        }elseif (isset(ReturnCode::$return_code[$code])){
            $return_data['msg'] = ReturnCode::$return_code[$code];
        }
        return $return_data;
    }
    /**
     * 没有返回数据的返回值
     * @param   string  $code
     * @param   string  $msg
     * @return  array
     **/
    static public function showReturnCodeWithOutData($code = '', $msg = '')
    {
        return self::showReturnCode($code,[],$msg);

    }
    /**
     * 只接收指定字段
     * @param   array   $data
     * @return  array
     **/
    public function buildParam($array=[]){
        $data=[];
        if(is_array($array)){
            foreach ($array as $item=>$value){
                $data[$item] = $this->request->param($value);
            }
        }
        return $data;
    }
    /**
     * 制作并图片
     */
    public function makeOutImg($str,$config = []){
        header('Content-Type: image/png');
        if(empty($config)){
            $config = [
                'logo'          => true,
                'logo_url'      => './static/public/img/logo.png',
                'logo_size'     => 80,
            ];
        }
        $qr_code = new QrcodeServer($config);
        $qr_img = $qr_code->createServer($str);
        ob_clean();
        echo $qr_img;
        exit;
    }
    /**
     * 进行model操作
     * power by HK
     * @param $param_data       数据
     * @param $validate_name    验证器
     * @param $model_name       model
     * @param $action_name      model的方法
     * return ===
     **/
    public function doModelAction($param_data,$validate_name = false, $model_name = false,$action_name='editData'){
        if ($validate_name != false) {
            $result = $this->validate($param_data, $validate_name);
            if (true !== $result) return $this->showReturnCodeWithOutData(1003,  $result);
        }
        $model_edit = model($model_name);
        if (!$model_edit) return $this->showReturnCode(1010);
        return $model_edit->$action_name($param_data);
    }

    /**
     * 进行数据库修改操作
     * power by HK
     * @param bool $parameter       获取传入值
     * @param bool $validate_name   验证器名
     * @param bool $model_name      model名
     * @param array $save_data      修改值
     * @return array
     * 用法1  $this->editData(false,$validate_name,$model_name,$save_data);//传入指定值进行操作
     * 用法2  $this->editData(ture,$validate_name,$model_name);//自动接受参数并进行操作
     * 用法3  $this->editData($data,$validate_name,$model_name);//接受指定字段并进行操作
     */
    protected function editData($parameter = false, $validate_name = false, $model_name = false, $save_data = [])
    {
        if (empty($save_data)) {
            if ($parameter != false && is_array($parameter)) {
                $data = $this->buildParam($parameter);
            } else {
                $data = $this->request->post();
            }
        } else {
            $data = $save_data;
        }
        if (!$data) return $this->showReturnCode(1004);
        if ($this->checkLoginToken() && !isset($data['uid'])) $data['uid'] = $this->uid;
        if ($validate_name != false) {
            $result = $this->validate($data, $validate_name);
            if (true !== $result) return $this->showReturnCodeWithOutData(1003,$result );
        }
        $model_edit = Loader::model($model_name);
        //dump($model_edit);
        if (!$model_edit) return $this->showReturnCode(1010);
        return $model_edit->editData($data);
    }

    /**
     * token验证
     * @return bool
     */
    protected function checkLoginToken(){
        return true;
    }
}