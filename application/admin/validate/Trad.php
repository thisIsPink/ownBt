<?php

namespace app\admin\validate;

use think\Validate;

class Trad extends Validate
{

    protected $rule = [
        'id'=>'number',
        'user'=>'require|number',
        'time'=>'require|number',
        'cash'=>'require|number',
        'currency'=>'require|number',
        'money'=>'require|float',
        'balance'=>'require|float',
        'virtual_amount'=>'require|float',
        'state'=>'require|number|max:1'
    ];

    protected $field = [
        'user'=>'用户',
        'time'=>'时间',
        'cash'=>'法币',
        'currency'=>'虚拟货币',
        'money'=>'金额',
        'balance'=>'上次的量',
        'virtual_amount'=>'虚拟货币量',
    ];
    protected $scene= [
        'add'=>['user','time','cash','currency','money','balance','virtual_amount'],
        'valstate'=>['id','state']
    ];
}
