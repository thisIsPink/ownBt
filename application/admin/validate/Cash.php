<?php

namespace app\admin\validate;

use think\Validate;

class Cash extends Validate
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
        'symbol'=>'require|max:3'
    ];
    protected $field = [
        'name'  => '名称',
        'symbol'   => '符号'
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $scene = [
        'basis'=>['name','symbol'],
        'valname'=>['name','id'],
        'valsymbol'=>['symbol','id']
    ];
}
