<?php
namespace app\admin\controller;
class Json{
    private $arr_str=["code"=>0,"msg"=>"","count"=>0,"data"=>[]];
    private function set_data($data,$num){
        $this->arr_str['data']=$data;
        $this->arr_str['count']=$num;
    }
    private function out(){
        echo json_encode($this->arr_str);
    }
    public function user(){
        $page=input('page')?input('page'):1;
        $limit=input('limit')?input('limit'):20;
        $data=db('user')->field('uid id,user,reg_time,money,remarks,business,state')->page($page,$limit)->select();
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
}
?>