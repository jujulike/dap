<?php
// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------
namespace app\admin\model;
use think\Model;

/**
 * 菜单模型
 */

class Menu extends Model { 
	protected $autoWriteTimestamp = false;
	protected $auto = ['title'];
	// 新增
	protected $insert = ['status'=>1];
	//属性修改器
	protected function setTitleAttr($value, $data)
	{
		return htmlspecialchars($value);
	} 
}