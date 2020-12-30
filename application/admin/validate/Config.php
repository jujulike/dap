<?php
 
namespace app\admin\validate;
use think\Validate; 

class Config extends Validate{
    // 验证规则
    protected $rule = [
        ['name', 'require|/^[a-zA-Z]\w{0,39}$/|unique:Config', '配置标识必须|标识不合法|标识已经存在'],
        ['title', 'require', '配置标题必须填写'],
        
    ];  

}