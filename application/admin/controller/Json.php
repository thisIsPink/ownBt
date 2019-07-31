<?php
namespace app\admin\controller;
class Json{
    private $arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
    private function set_data($data=[],$num=0){
        $this->arr_str['data']=$data;
        $this->arr_str['count']=$num;
    }
    private function out(){
        echo json_encode($this->arr_str);
    }
    public function user(){
        $page=input('page')?input('page'):1;
        $limit=input('limit')?input('limit'):20;
        $data=db('user')->alias('u')->leftJoin('cash c','u.cash=c.id')->field('uid id,user,phone,email,reg_time,money,remarks,business,key,c.name,symbol,btc,eth,eos,state,cash')->page($page,$limit)->select();
        $num=db('user')->count();
        $this->set_data($data,$num);
        $this->out();
    }
    public function coin(){
        $page=input('page')?input('page'):1;
        $limit=input('limit')?input('limit'):20;
        $data=db('coin')->field('uid id,name,smallest_unit,min_withdraw,publisher,state')->page($page,$limit)->select();
        $num=db('user')->count();
        $this->set_data($data,$num);
        $this->out();
    }
    public function cashList(){
        $page=input('page')?input('page'):1;
        $limit=input('limit')?input('limit'):20;
        $data=db('cash')->page($page,$limit)->select();
        $num=db('cash')->count();
        $this->set_data($data,$num);
        $this->out();
    }
    public function currencyList(){
        $page=input('page')?:1;
        $limit=input('limit')?:20;
        $data=db('currency')->page($page,$limit)->select();
        $num=db('currency')->count();
        $this->set_data($data,$num);
        $this->out();
    }
    public function proportion(){
        $cash=input('cash');
        if($cash!=null) {
            $page=input('page')?:1;
            $limit=input('limit')?:20;
            $data=db('proportion')->alias('pro')->join('currency c','pro.currency=c.id')->where('cash',$cash)->page($page,$limit)->field('pro.id,c.name,proportion')->select();
            $num=db('proportion')->where('cash',$cash)->count();
            $this->set_data($data,$num);
        }
        $this->out();
    }
    public function proportionCashList(){
        $cash=input('cash');
        if($cash!=null) {
            $data=db('proportion')->alias('pro')->join('currency c','pro.currency=c.id')->where('cash',$cash)->where('type','in','1,2')->field('currency,c.name,proportion')->select();
            $this->set_data($data,0);
        }
        $this->out();
    }
    public function userRecord(){
        $uid=input('user');
        if($uid!=null){
            $page=input('page')?:1;
            $limit=input('limit')?:20;
            $data=db('trad')->alias('t')->leftJoin('currency c','t.currency=c.id')->where('user',$uid)->page($page,$limit)->field('t.id,time,c.name,c.type,balance,money,state')->select();
            $num=db('trad')->where('user',$uid)->count();
            $this->set_data($data,$num);
        }
        $this->out();
    }
    public function txRecode(){
        $page=input('page')?:1;
        $limit=input('limit')?:20;
        $data=db('order')->alias('o')->join('user u','u.uid=o.user')->join('cash c','c.id=u.cash')->field('uid,business user,c.name coin,o.money,o.fee,o.time,o.state')->page($page,$limit)->select();
        $num=db('order')->count();
        $this->set_data($data,$num);
        $this->out();
    }
}
?>