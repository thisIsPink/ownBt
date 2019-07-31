<?php

namespace app\admin\validate;

use think\Validate;

class Proportion extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'id'=>'number',
        'cash'=>'require|max:5|number',
        'currency'=>'require|max:5|number',
        'proportion'=>'require|float'
    ];
    protected $field = [
        'cash'  => '法币',
        'currency'   => '虚拟币',
        'proportion'  => '比例'
    ];


    protected $scene = [
        'add'=>['cash','currency','proportion'],
        'valcash'=>['cash','id'],
        'valcurrency'=>['currency','id'],
        'valproportion'=>['proportion','id']
    ];
}
