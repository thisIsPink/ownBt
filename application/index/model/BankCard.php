<?php
namespace app\index\model;

use think\Model;
class BankCard extends Model
{
    protected $pk = 'id';
    public function add($data){
        $yuan=$this->where('card',$data['card'])->find();
        if($yuan!=null){
            return ['code'=>'1002','msg'=>'该卡号已存在'];
        }
        $flag=$this->insert($data);
        if($flag){
            return ['code'=>'1001','msg'=>'操作成功'];
        }else{
            return ['code'=>'1003','msg'=>'添加失败'];
        }
    }
}