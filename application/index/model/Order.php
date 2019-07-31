<?php
namespace app\index\model;

use think\Model;
class Order extends Model
{
    protected $pk = 'id';
    public function add($data){
        $flag=$this->insert($data);
        if($flag){
            return ['code'=>'1001','msg'=>'操作成功'];
        }else{
            return ['code'=>'1003','msg'=>'添加失败'];
        }
    }
}