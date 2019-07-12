<?php
namespace app\index\model;

use think\Model;
use think\Db;
class Coin extends Model
{
    protected $pk = 'id';
    protected $table = 'op_proportion';
    public function getCoinList($coin = ''){
        if(empty($coin)) return false;
        if(is_numeric($coin)){
            return $this->alias('pro')->join('currency cu','cu.id=pro.currency')->field('cu.name')->where('cash',$coin)->select();
        }
        return $this->alias('pro')->join('cash c','c.id=pro.cash')->join('currency cu','cu.id=pro.currency')->field('cu.name')->where('name',$coin)->select();
    }
    public function getAccount($coin='',$key=''){
        if(empty($coin)) return false;
        if($coin='EOS'){
            return DB::name('account')->find()['eos'];
        }else{
            if(empty($key)) return false;

        }
    }
    public function getCoinType($coin){
        $type=Db::name('currency')->field('type')->where('name',$coin)->find()['type'];
        switch ($type){
            case '1':
                return 'BTC';
            case '2':
                return 'ETH';
            case '3':
                return 'EOS';
        }
    }
    public function cashToCurrency($cash,$currency){
        if(is_numeric($cash)&&is_numeric($currency)){
            return $this->where(['cash'=>$cash,'currency'=>$currency])->find()['proportion'];
        }elseif(!is_numeric($cash)&&is_numeric($currency)){
            return $this->alias('pro')->join('cash c','c.id=pro.cash')->where(['c.name'=>$cash,'currency'=>$currency])->find()['proportion'];
        }elseif(is_numeric($cash)&&!is_numeric($currency)){
            return $this->alias('pro')->join('currency cu','cu.id=pro.currency')->where(['cash'=>$cash,'cu.name'=>$currency])->find()['proportion'];
        }elseif(!is_numeric($cash)&&!is_numeric($currency)){
            return $this->alias('pro')->join('cash c','c.id=pro.cash')->join('currency cu','cu.id=pro.currency')->where(['c.name'=>$cash,'cu.name'=>$currency])->find()['proportion'];
        }
    }
    public function getRecode($coin){
        $data=Db::name('currency')->where('name',$coin)->find();
        if($data){
            return $data['recode'];
        }else{
            return false;
        }
    }
}