<?php
namespace app\index\model;

use think\Model;
use think\Db;
class User extends Model
{
    protected $pk = 'uid';
    public function getOneData($where = [],$field=''){
        if(empty($field)){
            return $this->alias('u')->join('cash c','u.cash=c.id')->where($where)->find();
        }else{
            return $this->alias('u')->join('cash c','u.cash=c.id')->field($field)->where($where)->find();
        }
    }
    public function moneySub($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误', ];
        $uid=$data[$this->pk];
        unset($data[$this->pk]);
        $user=$this->where($this->pk,$uid)->find();
        if($data['money']>$user['money']){
            return ['code'=>'1006','msg'=>'余额不足'];
        }
        $money=bcsub($user['money'],$data['money'],2);
        if($money==$user['money']){
            return ['code'=>'1005','msg'=>'金额未变动'];
        }
        $save=$this->where($this->pk,$uid)->update(['money'=>$money]);
        if($save == null || $save == false){
            return ['code'=>'1004','msg'=>'未操作金额'];
        }else{
            return ['code'=>'1001','data'=>$money,'msg'=>'数据更新成功'];
        }
    }
}