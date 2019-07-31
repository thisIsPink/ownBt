<?php

namespace app\index\validate;

use think\Validate;

class BankCard extends Validate
{

    protected $rule = [
        'id'=>'number',
        'user'=>'require|max:10|number',
        'phone'=>'require|mobile',
        'card'=>'require|max:25|number',
        'name'=>'require|max:24',
        'id_number'=>'require|max:25',
        'bank'=>'require|max:50',
        'state'=>'require|number|max:1'
    ];

    protected $field = [
        'user'=>'用户',
        'phone'=>'手机',
        'name'=>'名称',
        'card'=>'卡号',
        'id_number'=>'身份证号',
        'bank'=>'银行'
    ];
    protected $scene= [
        'add'=>['user','phone','name','card','id_number','bank','state']
    ];
}
