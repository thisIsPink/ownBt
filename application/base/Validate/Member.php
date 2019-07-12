<?php
namespace app\base\validate;

class Member extends \think\Validate
{
    //详情规则看https://www.kancloud.cn/manual/thinkphp5/129356
    //规则
    protected $rule = [
        ['mobile','regex:1[34578]\d{9}','请输入正确的手机号码'],
        ['userid','alphaDash|length:4,25', '请输入正确的用户名(为字母和数字，下划线_及破折号-)|用户名长度必须在4--20位之间'],
        ['userpwd','require|alphaDash|length:4,25', '密码不能为空|请输入正确的密码(为字母和数字，下划线_及破折号-)|密码长度必须在4--20位之间'],
        ['code','require|regex:\d{5}', '手机验证码不能为空|请输入正确的手机验证码'],
    ];
    //验证
    protected $scene = [
        'login'    => ['userid','userpwd','mobile'],
        'reg' =>['mobile','userpwd','code']
    ];
}