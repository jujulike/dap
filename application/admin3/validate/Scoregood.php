<?php
 
namespace app\admin\validate;
use think\Validate; 

class Scoregood extends Validate{
    // 验证规则
    protected $rule = [
        ['title', 'require', '商品名称必须填写'],
        ['content', 'require', '商品详情必须填写'],
    ];  

}