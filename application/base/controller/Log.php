<?php
namespace app\base\controller;

class Log extends Base
{
    protected $error;             //出错时候的记录
    protected $log=[];            //要保存的记录
    protected $saveLog = false ;
    protected function showReturnCodeWithSaveLog($code = '', $data = [], $msg = ''){
        $this->saveLog = true ;
        $this->addLog($code,$msg);
        return self::showReturnCode($code, $data, $msg);
    }
    protected function addLog($code='',$msg=''){
        $this->log[] =[
            'uid' => $this->uid,
            'url' => $this->request->url(true),
            'method' => $this->request->method(),
            'data' => $this->getData(),
            'ip' => $this->request->ip(),
            'code'=>$code,
            'desc' => $msg,
        ];
    }
    protected function toSaveLog(){
        $this->saveLog = true ;
        $this->addLog();
    }
    protected function getData(){
        if ($this->request->isPost()){
            return $this->request->post();
        }else{
            return $this->request->get();
        }
    }
    protected function saveLogAction(){
        if (!empty($this->log)){
            foreach($this->log as $value){
                dump($value);
            }
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        //记录日志
        if (!empty($this->log) && $this->saveLog == true){
            $this->saveLogAction();
        }
    }
}