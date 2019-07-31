<?php
namespace app\admin\model;

use think\Model;

class Cash extends Model
{
    protected $pk = 'id';
    public function add($data){
        $save=$this->allowField(true)->insert($data);
        if($save == 0 || $save == false){
            $res=[  'code'=> 1009,  'msg' => '数据更新失败', ];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功',  ];
        }
        return $res;
    }
    public function edit($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误', ];
        $id=$data[$this->pk];
        unset($data[$this->pk]);
        $save=$this->where($this->pk,$id)->update($data);
        if($save == 0 || $save == false){
            $res=[  'code'=> 1009,  'msg' => '数据更新失败', ];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功',  ];
        }
        return $res;
    }
    public function del($data){
        if(empty($data[$this->pk])) return [ 'code'=> 1003,  'msg' => '请求参数错误', ];
        $id=$data[$this->pk];
        $save=$this->where($this->pk,$id)->delete();
        if($save == 0 || $save == false){
            $res=[  'code'=> 1009,  'msg' => '数据更新失败', ];
        }else{
            $res=[  'code'=> 1001,  'msg' => '数据更新成功',  ];
        }
        return $res;
    }
}