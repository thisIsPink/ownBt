<?php

namespace app\index\validate;

use think\Validate;

class User extends Validate
{

    protected $rule = [
        'uid'=>'number',
        'user'=>'require|max:25|alphaDash',
        'phone'=>'require|mobile',
        'email'=>'require|email',
        'password'=>'require|max:60',
        'money'=>'require|float',
        'reg_time'=>'require|number',
        'remarks'=>'require|max:200',
        'business'=>'require|max:100',
        'state'=>'in:1,2',
        'key'=>'require|max:30|alphaNum',
        'cash'=>'require|max:3|number',
        'btc'=>'require|max:40|alpha',
        'eth'=>'require|max:40|alpha',
        'eos'=>'require|max:40|alphaNum',
    ];

    protected $field = [
        'user'=>'用户名',
        'phone'=>'手机',
        'email'=>'邮箱',
        'remarks'=>'备注',
        'business'=>'商家',
        'cash'=>'法币',
        'btc'=>'BTC',
        'eth'=>'ETH',
    ];
    protected $scene= [
        'login'=>['user','password'],
        'edit'=>['phone','email','cash','business','btc','eth','uid'],
        'valstate'=>['uid','state'],
        'moneyAdd'=>['uid','money'],
    ];
}
