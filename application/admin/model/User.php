<?php
namespace app\admin\model;

use think\Model;

class User extends Model
{
    protected $pk = 'uid';
    public function add($data){
        return $this->insert($data);
    }
}