<?php
namespace app\admin\controller;
use think\Controller;
class Index extends Controller{
    private function is_login(){
        if(session('admin')=='trues'){
            return true;
        }else{
            $this->success('请登陆', '/admin/index/login');
        }
    }
    public function login()
    {
        return $this->fetch();
    }
    public function index(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function member(){
        if($this->is_login()){
            return $this->fetch();
        }
    }
    public function member_edit(){
        if($this->is_login()) {
            if(input('id')!=null){
                $user=db('user')->where('uid',input('id'))->find();
                $cash=db('cash')->select();
                $this->assign('data',$user);
                $this->assign('cash',$cash);
                return $this->fetch();
            }else{
//                abort(404, '页面异常');
            }
        }
    }
    public function cash(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function currency(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function proportion(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function coin(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function coin_add(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function recode(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function tx(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
}
?>