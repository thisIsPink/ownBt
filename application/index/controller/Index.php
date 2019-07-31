<?php
namespace app\index\controller;

use app\index\model\Coin;
use app\index\model\User;
use think\Controller;
use app\base\controller\Auth;
use think\App;
use think\facade\Request;

class Index extends Controller
{
    private function is_login(){
        if(is_numeric(session('user'))){
            return true;
        }else{
            $this->success('请登陆', '/index/index/index');
        }
    }
    protected $auth = null;
    function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->auth=new Auth();
    }

    public function index(){
        if(is_numeric(session('user'))) {
            $page=input('page');
            $limit=input('limit');
            if((int)$limit>40){
                $limit=40;
            }
            $data=db('user')->where('uid',session('user'))->find();
            $num=db('trad')->where('user',session('user'))->count();
            $trad=db('trad')->where('user',session('user'))->page($page,$limit)->order('time','desc')->select();
            $retuenData=['money'=>$data['money'],'frozen'=>$data['frozen'],'num'=>$num];
            $this->assign('trad',$trad);
            $this->assign('data',$retuenData);
            $this->assign('id',md5(session('user')));
            return $this->fetch('home');
        }else{
            return $this->fetch('login');
        }
    }
    public function withdrawal(){
        if($this->is_login()) {
            $data=db('bank_card')->field('id,card,name')->where('user',session('user'))->select();
            if($data!=null){
                foreach ($data as $key=>$value){
                    $data[$key]['card']=substr($data[$key]['card'], 0, 4) . "******" . substr($data[$key]['card'], -4);
                }
            }
            $money=db('user')->field('money')->where('uid',session('user'))->find()['money'];
            $this->assign('money',$money);
            $this->assign('data',$data);
            return $this->fetch();
        }
    }
    public function bankcard(){
        if($this->is_login()) {
            return $this->fetch();
        }
    }
    public function merchantQrCode(){
        $loginDate=$this->auth->getLoginGlobal();
        if($loginDate){
            $key=$loginDate['key'];
        }else{
            $key=Request::get('key');
            if(!$key) return '没有图片';
        }
        var_dump(config('url.')['ht'].'?key='.$key);
        //$this->auth->makeOutImg(config('url.')['ht'].'?key='.$key,['logo'=>false]);
//        $qr_url=json_encode([
//            'action'=>'transfer',
//            'address'=>$account,
//            'amount'=>'0',
//            'blockchain'=>'EOS',
//            'chainid'=>'aca376f206b8fc25a6ed44dbdc66547c36c6c33e3a119ffbeaef943642f0e906',
//            'contract'=>$publisher,
//            'precision'=>4,
//            'protocol'=>'ScanProtocol',
//            'symbol'=>$coin,
//            'memo'=>$tag
//        ]);
    }
    public function coinList(){
        $param=['key'=>'key'];
        $param_data=$this->auth->buildParam($param);
        $user = new User();
        $userInfo=$user->getOneData(['key'=>$param_data['key']],'cash,business,symbol');
        if($userInfo!=null){
            $data=['business'=>$userInfo['business'],'symbol'=>$userInfo['symbol']];
            $coin=new Coin();
            $coinList=$coin->getCoinList($userInfo['cash']);
            $this->assign('coin',$coinList);
            $this->assign('data',$data);
            return $this->fetch('inputMoney');
        }
        return '<h1>没有该商家</h1>';
//        var_dump($data);
    }
    public function createOrder(){
        $param=['coin'=>'coin','money'=>'money','key'=>'key'];
        $paramData=$this->auth->buildParam($param);
        foreach ($paramData as $value){
            if($value==null){
                return 'false';
            }
        }
        if($paramData['coin']=='EOS'||$paramData==null){
            return 'true';
        }else{
            return 'false';
        }
    }
    public function pay(){
        $param=['coin'=>'coin','money'=>'money','key'=>'key'];
        $paramData=$this->auth->buildParam($param);
        //验证字段
        foreach ($paramData as $value){
            if($value==null){
                return '您的页面出错了';
            }
        }
        $user=new User();
        $coin=new Coin();
        $info=$user->getOneData(['key'=>$paramData['key']],'cash,btc,eth,eos,business');
        //没有商家
        if(!$info)return 'false';
        $coinChain=$coin->getCoinType($paramData['coin']);
        switch ($coinChain){
            case 'EOS':
                $account=$coin->getAccount($paramData);
                $memo=$info['eos'];
                break;
            case 'BTC':
                $account=$info['btc'];
                $memo='';
                break;
            case 'eth':
                $account=$info['eth'];
                $memo='';
                break;
            default:
                $account=false;
        }
        if($account){
            $recode=$coin->getRecode($paramData['coin']);
        }else{
            $recode=false;
        }
        $money=$coin->cashToCurrency($info['cash'],$paramData['coin']);
        $money=$money?$money*$paramData['money']:false;
        if($account&&$recode&&$money){
            $recode=str_replace('<{account}>',$account,$recode);
            $recode=str_replace('<{money}>',$money,$recode);
            if($memo!=''){
                $recode=str_replace('<{memo}>',$memo,$recode);
                $recode=str_replace('<{coin}>',$paramData['coin'],$recode);
                $this->assign('memo',$memo);
            }
            $recode='/index/index/payRecode?data='.$recode;
            $this->assign('recode',$recode);
            $this->assign('account',$account);
            $this->assign('money',$money);
            $this->assign('business',$info['business']);
        }
        return $this->fetch();
    }
    public function payRecode(){
        $this->auth->makeOutImg(input('data'),['logo'=>false]);
    }
    public function eospark(){
        $a=$this->send_post('https://open-api.eos.blockdog.com/v1/third/get_account_transfer','{"account_name":"soul2master4","page":1}');
        $a=json_decode($a,true);
//        var_dump($a);
        if(isset($a['list'])&&$a['list']!=null){
            $i=0;
            foreach ($a['list'] as $value){
                $money=$value['data']['quantity']+0;
                $coin_token=$value['account'];
                $txid=$value['id'];
                $form=$value['data']['from'];
                $time=strtotime("+8 hours",strtotime($value["blockTime"]));
            }
        }
    }
    public function send_post($url, $data) {
        //初使化init方法
        $ch = curl_init();
        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);
        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);
        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $headers=array('accept: application/json;charset=UTF-8','apikey: 5b4added-e80c-41fb-b5a9-16269d2de79b','content-type: application/json');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //发送请求
        $output = curl_exec($ch);
        //关闭curl
        curl_close($ch);
        //返回数据
        return $output;
    }
}