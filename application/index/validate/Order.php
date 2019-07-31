<?php

namespace app\index\validate;

use think\Validate;

class Order extends Validate
{

    protected $rule = [
        'id'=>'number',
        'user'=>'require|max:10|number',
        'money'=>'require|float',
        'time'=>'require|max:16|number',
        'state'=>'require|max:1|number',
        'fee'=>'require|float',
        'card'=>'require|number'
    ];

    protected $field = [
        'user'=>'用户',
        'money'=>'金额',
        'time'=>'时间',
        'state'=>'状态',
        'fee'=>'手续费',
        'card'=>'银行'
    ];
    protected $scene= [
        'add'=>['user','money','time','state','fee','card']
    ];
}
