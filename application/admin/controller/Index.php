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
}
?>