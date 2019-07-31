<?php

namespace app\admin\validate;

use think\Validate;

class Currency extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'=>'number',
        'name'=>'require|max:10',
        'type'=>'require|in:1,2,3',
        'recode'=>'require'
    ];
    protected $field = [
        'name'  => '名称',
        'type'   => '类型',
        'recode'  => '二维码规则'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'type'=>'类型只能为1(BTC),2(ETH),3(EOS)'
    ];

    protected $scene = [
        'add'=>['name','type','recode'],
        'valname'=>['name','id'],
        'valtype'=>['type','id'],
        'valrecode'=>['recode','id']
    ];
}
