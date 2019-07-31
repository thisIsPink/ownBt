<?php
namespace app\admin\model;

use think\Model;

class User extends Model
{
    protected $pk = 'uid';
    public function add($data){
        if($this->where('user',$data['user'])->whereOr('eos',$data['eos'])->whereOr('key',$data['key'])->find()!=null){
            $res=[  'code'=> 1008,  'msg' => '用户名已存在'];
        }else{
            $save=$this->allowField(true)->insert($data);
            if($save == 0 || $save == false){
                $res=[  'code'=> 1009,  'msg' => '数据更新失败'];
            }else{
                $res=[  'code'=> 1001,  'msg' => '数据更新成功'];
            }
        }
        return $res;
    }
    public function edit($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误' ];
        $id=$data[$this->pk];
        unset($data[$this->pk]);
        $save=$this->where($this->pk,$id)->update($data);
        if($save == 0 || $save == false){
            $res=[  'code'=> 1009,  'msg' => '数据更新失败'];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功'];
        }
        return $res;
    }
    public function del($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误', ];
        $id=$data[$this->pk];
        $save=$this->where($this->pk,$id)->delete();
        if($save == 0 || $save == false){
            $res=[  'code'=> 1009,  'msg' => '数据更新失败'];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功'];
        }
        return $res;
    }
    public function getOne($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误', ];
        $uid=$data[$this->pk];
        $save=$this->where($this->pk,$uid)->find();
        if($save == null || $save == false){
            return false;
        }else{
            return $save;
        }
    }
    public function moneyAdd($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误', ];
        $uid=$data[$this->pk];
        unset($data[$this->pk]);
        $user=$this->where($this->pk,$uid)->find();
        $money=bcadd($user['money'],$data['money'],2);
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
            return ['code'=>'1001','msg'=>'数据更新成功'];
        }
    }
    public function frozen($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误'];
        $uid=$data[$this->pk];
        unset($data[$this->pk]);
        $user=$this->where($this->pk,$uid)->find();
        if($data['money']>$user['money']){
            return ['code'=>'1006','msg'=>'余额不足'];
        }
        $money=bcsub($user['money'],$data['money'],2);
        $frozenMoney=bcadd($user['frozen'],$data['money'],2);
        if($money==$user['money']){
            return ['code'=>'1005','msg'=>'金额未变动'];
        }
        $save=$this->where($this->pk,$uid)->update(['money'=>$money,'frozen'=>$frozenMoney]);
        if($save == null || $save == false){
            return ['code'=>'1004','msg'=>'未操作金额'];
        }else{
            return ['code'=>'1001','msg'=>'数据更新成功'];
        }
    }
    public function thaw($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误'];
        $uid=$data[$this->pk];
        unset($data[$this->pk]);
        $user=$this->where($this->pk,$uid)->find();
        if($data['money']>$user['frozen']){
            return ['code'=>'1006','msg'=>'解冻金额出错'];
        }
        $money=bcadd($user['money'],$data['money'],2);
        $frozenMoney=bcsub($user['frozen'],$data['money'],2);
        if($money==$user['money']){
            return ['code'=>'1005','msg'=>'金额未变动'];
        }
        $save=$this->where($this->pk,$uid)->update(['money'=>$money,'frozen'=>$frozenMoney]);
        if($save == null || $save == false){
            return ['code'=>'1004','msg'=>'未操作金额'];
        }else{
            return ['code'=>'1001','msg'=>'数据更新成功'];
        }
    }
}