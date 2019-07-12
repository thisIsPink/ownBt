<?php
namespace app\admin\controller;
use app\base\controller\Auth;
use app\base\controller\Base;
use think\App;
use think\Controller;
class Sub extends Controller
{
    protected $auth;
    function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->auth=new Auth();
    }
    private $arr_str=["flag"=>false,"msg"=>'',"data"=>[]];
    private function state_ok(){
        $this->arr_str["flag"]=true;
        //正常
    }
    private function set_error($str){
        $this->arr_str['msg']=$str;
    }
    private function set_data($data){
        $this->state_ok();
        $this->arr_str["data"]=$data;
    }
    private function is_out($data){
        if($data){
            $this->set_data($data);
        }
        echo json_encode($this->arr_str);
    }
    private function out(){
        echo json_encode($this->arr_str);
    }
    public function login()
    {
        $name=input('user');
        $pwd=input('pwd');
        if($name!=null&&$pwd!=null){
            if($name=='user'&&$pwd=='123456'){
                session('admin','trues');
                $this->state_ok();
            }else{
                $this->set_error('账号或密码不正确');
            }
        }
        $this->out();
    }
    public function coin_add(){
        $name=input('name');
        $small=input('small');
        $min_withdraw=input('min_withdraw');
        if($name!=null&&$small!=null&&$min_withdraw!=null){
            db('coin')->insert(['name'=>strtoupper($name),'smallest_unit'=>$small,'min_withdraw'=>$min_withdraw,'state'=>'1']);
            $this->state_ok();
        }
        $this->out();
    }
    public function coin_edit(){
        $field=input('field');
        $value=input('value');
        $id=input('id');
        if($field!=null&&$value!=null&&$id!=null){
            if(db('coin')->where('Id',$id)->update(['state'=>"0"])){
                db('coin')->where('Id',$id)->update([$field=>$field=='name'?strtoupper($value):$value]);
                $this->state_ok();
            }
        }
        $this->out();
    }
    public function coin_state_down(){
        $id=input("id");
        if($id!=null){
            if(db('coin')->where('Id',$id)->update(['state'=>"0"])){
                $this->state_ok();
            }
        }
        $this->out();
    }
    public function coin_state_up(){
        $id=input("id");
        if($id!=null){
            if(db('coin')->where('Id',$id)->update(['state'=>"1"])){
                $this->state_ok();
            }
        }
        $this->out();
    }
    public function user_add(){
        $param=['user'=>'user','business'=>'business'];
        $paramData=$this->auth->buildParam($param);
        $paramData['key']=$this->auth->create_uid('OWN');
        $paramData['eos']=$this->auth->create_uid('PAY');
        $check=$this->auth->doModelAction($paramData,'admin/User.add','User','add');
        if($check){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function user_info_edit(){
        $id=input("id");
        $value=input('value');
        $field=input('field');
        if($id!=null&&$value!=null&&$field!=null){
            if(db('user')->where('Id',$id)->update([$field=>$value])){
                $this->state_ok();
            }
        }
        $this->out();
    }
    public function user_state_down(){
        $id=input('id');
        if($id!=null){
            if(db('user')->where('Id',$id)->update(['state'=>0])){
                $this->state_ok();
            }
        }
        $this->out();
    }
    public function user_state_up(){
        $id=input('id');
        if($id!=null){
            if(db('user')->where('Id',$id)->update(['state'=>1])){
                $this->state_ok();
            }
        }
        $this->out();
    }
}
