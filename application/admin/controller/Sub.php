<?php
namespace app\admin\controller;
use app\base\controller\Auth;
use think\App;
use think\Controller;
use think\Db;

class Sub extends Controller
{
    protected $auth;
    function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->auth=new Auth();
    }
    private $arr_str=["flag"=>false,"msg"=>'',"data"=>[]];
    private function setMsg($msg){
        $this->arr_str['msg']=$msg;
    }
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
    public function userAdd(){
        $param=['user'=>'user','cash'=>'cash','business'=>'business'];
        $paramData=$this->auth->buildParam($param);
        $paramData['key']=$this->auth->create_uid('OWN');
        $paramData['eos']=$this->auth->create_uid('PAY');
        $paramData['money']=0.00;
        $paramData['reg_time']=time();
        $paramData['state']=1;
        $paramData['password']=md5(config('base')['defaultPassword']);
        $check=$this->auth->doModelAction($paramData,'admin/User.add','User','add');
        return $check;
    }
    public function userEdit(){
        $param=['phone'=>'phone','email'=>'email','cash'=>'cash','business'=>'business','remarks'=>'remarks','btc'=>'btc','eth'=>'eth','uid'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,'admin/User.edit','User','edit');
        return $check;
    }
    public function userStateDown(){
        $param=['uid'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $paramData['state']=2;
        $check=$this->auth->doModelAction($paramData,'admin/User.valstate','User','edit');
        if($check['code']=='1001'){
            $this->state_ok();
        }else{
            $this->setMsg($check['msg']);
        }
        $this->out();
    }
    public function moneySendCurrency(){
        $param=['uid'=>'id','money'=>'money','currency'=>'currency'];
        $paramData=$this->auth->buildParam($param);
        if($paramData['money']==0)return [  'code'=> 1002,  'msg' => '请输入金额'];
        $userInfo=$this->auth->doModelAction(['uid'=>$paramData['uid']],false,'User','getOne');
        if(!$userInfo)return [  'code'=> 1003,  'msg' => '没有该用户'];
        $order=date('YmdHis').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        if($paramData['currency']!=null){
            $porinfo=$this->auth->doModelAction(['currency'=>$paramData['currency'],'cash'=>$userInfo['cash']],false,'proportion','getOneInfo');
            if(!$porinfo)return [  'code'=> 1004,  'msg' => '货币错误'];
            $money=$paramData['money']*$porinfo['proportion'];
            $addData=['money'=>$money,'time'=>time(),'cash'=>$userInfo['cash'],'currency'=>$paramData['currency'],'user'=>$paramData['uid'],'balance'=>$userInfo['money'],'virtual_amount'=>$paramData['money'],'state'=>1,'order'=>$order];
        }else{
            $money=$paramData['money'];
            $addData=['money'=>$money,'time'=>time(),'cash'=>$userInfo['cash'],'currency'=>0,'user'=>$paramData['uid'],'balance'=>$userInfo['money'],'virtual_amount'=>0,'state'=>1,'order'=>$order];
        }
        $money=$this->num_point($money,2);
        Db::startTrans();
        try {
            $check1=$this->auth->doModelAction(['uid'=>$paramData['uid'],'money'=>$money],'admin/User.moneyAdd','User','moneyAdd');
            $check2=$this->auth->doModelAction($addData,'admin/Trad.add','Trad','add');
            if($check2['code']=='1001'&&$check1['code']=='1001'){
                $check=$check1;
                $this->send_msg('send_msg',['time'=>$addData['time'],'money'=>$addData['money'],'state'=>$addData['state'],'order'=>$addData['order']],md5($paramData['uid']));
                Db::commit();
            }else{
                if($check1['code']!='1001'){
                    $check=$check1;
                }else{
                    $check=$check2;
                }
                throw new \Exception(json_encode($check));
            }
            // 提交事务
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $check=json_decode($e->getMessage(),true);
        }

        echo json_encode($check);
    }
    public function assetFrozen(){
        $param=['id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $paramData['state']='2';
        $info=$this->auth->doModelAction(['id'=>$paramData['id']],false,'Trad','getOne');
        Db::startTrans();
        try {
            $check1=$this->auth->doModelAction(['uid'=>$info['user'],'money'=>$money=$info['money']],'admin/User.moneyAdd','User','frozen');
            $check2=$this->auth->doModelAction($paramData,'admin/Trad.valstate','Trad','edit');
            if($check2['code']=='1001'&&$check1['code']=='1001'){
                $check=$check1;
                Db::commit();
            }else{
                if($check1['code']!='1001'){
                    $check=$check1;
                }else{
                    $check=$check2;
                }
                throw new \Exception(json_encode($check));
            }
            // 提交事务
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $check=json_decode($e->getMessage(),true);
        }
        if($check['code']=='1001'){
            $this->state_ok();
        }else{
            $this->setMsg($check['msg']);
        }
        $this->out();
    }
    public function assetThaw(){
        $param=['id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $paramData['state']='1';
        $info=$this->auth->doModelAction(['id'=>$paramData['id']],false,'Trad','getOne');
        Db::startTrans();
        try {
            $check1=$this->auth->doModelAction(['uid'=>$info['user'],'money'=>$info['money']],'admin/User.moneyAdd','User','thaw');
            $check2=$this->auth->doModelAction($paramData,'admin/Trad.valstate','Trad','edit');
            if($check2['code']=='1001'&&$check1['code']=='1001'){
                $check=$check1;
                Db::commit();
            }else{
                if($check1['code']!='1001'){
                    $check=$check1;
                }else{
                    $check=$check2;
                }
                throw new \Exception(json_encode($check));
            }
            // 提交事务
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            $check=json_decode($e->getMessage(),true);
        }
        if($check['code']=='1001'){
            $this->state_ok();
        }else{
            $this->setMsg($check['msg']);
        }
        $this->out();
    }
    public function userStateUp(){
        $param=['uid'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $paramData['state']=1;
        $check=$this->auth->doModelAction($paramData,'admin/User.valstate','User','edit');
        if($check['code']=='1001'){
            $this->state_ok();
        }else{
            $this->setMsg($check['msg']);
        }
        $this->out();
    }
    public function cashAdd(){
        $param=['name'=>'name','symbol'=>'symbol'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,'admin/Cash.basis','Cash','add');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function cashEdit(){
        $param=['field'=>'field','value'=>'value','id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $filedArr=['name','symbol'];
        if(!in_array($paramData['field'],$filedArr)) return 'false';
        $data=[$paramData['field']=>$paramData['value'],'id'=>$paramData['id']];
        $check=$this->auth->doModelAction($data,'admin/Cash.val'.$paramData['field'],'Cash','edit');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function cashDel(){
        $param=['id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,false,'Cash','del');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function currencyAdd(){
        $param=['name'=>'name','type'=>'type','recode'=>'memo'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,'admin/Currency.add','Currency','add');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return $check['msg'];
        }
    }
    public function currencyEdit(){
        $param=['field'=>'field','value'=>'value','id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $filedArr=['name','type','recode'];
        if(!in_array($paramData['field'],$filedArr)) return 'false';
        $data=[$paramData['field']=>$paramData['value'],'id'=>$paramData['id']];
        $check=$this->auth->doModelAction($data,'admin/Currency.val'.$paramData['field'],'Currency','edit');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return $check['msg'];
        }
    }
    public function currencyDel(){
        $param=['id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,false,'Currency','del');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function proportionAdd(){
        $param=['cash'=>'cash','currency'=>'currency','proportion'=>'pro'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,'admin/Proportion.add','Proportion','add');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return $check['msg'];
        }
    }
    public function proportionEdit(){
        $param=['field'=>'field','value'=>'value','id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $filedArr=['proportion'];
        if(!in_array($paramData['field'],$filedArr)) return 'false';
        $data=[$paramData['field']=>$paramData['value'],'id'=>$paramData['id']];
        $check=$this->auth->doModelAction($data,'admin/Proportion.valproportion','Proportion','edit');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return $check['msg'];
        }
    }
    public function proportionDel(){
        $param=['id'=>'id'];
        $paramData=$this->auth->buildParam($param);
        $check=$this->auth->doModelAction($paramData,false,'Proportion','del');
        if($check['code']=='1001'){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function txOk(){

    }
    public function txDown(){

    }
    private function num_point($num,$n){
        return sprintf("%1\$.4f", floor($num*pow(10,$n))/pow(10,$n));
    }
    private function send_msg($type,$data,$group){
        $push_api_url = "http://127.0.0.1:2131";
        $data = array(
            "type" => $type,
            "content" => json_encode($data),
            "group" => $group
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
}
