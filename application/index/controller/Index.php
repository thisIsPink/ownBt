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
    protected $auth = null;
    function __construct(App $app = null)
    {
        parent::__construct($app);
        $this->auth=new Auth();
    }

    public function index(){
        
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
}