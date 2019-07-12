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
}