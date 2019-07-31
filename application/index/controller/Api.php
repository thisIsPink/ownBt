<?php

namespace app\index\controller;

use app\base\controller\Auth;
use think\App;
use think\Controller;
use think\DB;

class Api extends Controller
{
    protected $auth = null;
    function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->auth=new Auth();
    }
    public function login(){
        $param=['user'=>'name','password'=>'password'];
        $paramData=$this->auth->buildParam($param);
        $paramData['password']=md5($paramData['password']);
        $check=$this->auth->doModelAction($paramData,'index/User.login','User','getOneData');
        if($check!=null){
            session('user',$check['uid']);
            echo json_encode($this->auth->showReturnCodeWithOutData('1001','登陆成功'));
        }else{
            echo json_encode($this->auth->showReturnCodeWithOutData('1002','账号名货密码错误'));
        }
    }
    public function addCard(){
        $param=['bank'=>'bank','name'=>'name','card'=>'card','id_number'=>'num','phone'=>'phone'];
        $paramData=$this->auth->buildParam($param);
        $paramData['user']=session('user');
        $paramData['state']=1;
        $check=$this->auth->doModelAction($paramData,'index/Bank_card.add','Bank_card','add');
        echo json_encode($check);
    }
    public function withdrawal(){
        $param=['card'=>'card','money'=>'money'];
        $paramData=$this->auth->buildParam($param);
        if(session('user')!=null){
            $paramData['user']=session('user');
        }else{
            return json_encode(['code'=>1004,'msg'=>'未登录']);
        }
        $paramData['time']=time();
        $paramData['state']=1;
        $paramData['fee']=0;
        $userMoney=db('user')->field('money')->where('uid',$paramData['user'])->find()['money'];
        if(db('bank_card')->find($paramData['card'])['user']!=session('user')){
            return json_encode(['code'=>1006,'msg'=>'持卡人和商家不和']);
        }
        //手续费
//        var_dump($paramData);
        Db::startTrans();
        try {
            $check1 = $this->auth->doModelAction($paramData, 'index/Order.add', 'Order', 'add');
            $check2 = $this->auth->doModelAction(['uid'=>$paramData['user'],'money'=>$paramData['money']],'index/User.moneyAdd','User','moneySub');
            if($check2['code']=='1001'&&$check1['code']=='1001'){
                $check=$check2;
                Db::commit();
            }else{
                if($check1['code']!='1001'){
                    $check=$check1;
                }else{
                    $check=$check2;
                }
                throw new \Exception(json_encode($check));
            }
        }catch (\Exception $e){
            Db::rollback();
            $check=json_decode($e->getMessage(),true);
        }
        echo json_encode($check);
    }
}