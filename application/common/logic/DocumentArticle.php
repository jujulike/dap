<?php
// +----------------------------------------------------------------------
// | Thinkphp [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 zfc000000 All rights reserved.
// +----------------------------------------------------------------------
// | Author: zfc000000
// +----------------------------------------------------------------------
namespace app\common\logic;
use think\Model;

/**
 * 文档模型子模型 - 文章模型
 */
class DocumentArticle extends Base{  

    /**
     * 获取文章的详细内容
     * @return boolean
     * Author: zfc000000
     */
    protected function getContent(){
        $type = input('post.type');
        $content = input('post.content');
        if($type > 1){//主题和段落必须有内容
            if(empty($content)){
                return false;
            }
        }else{  //目录没内容则生成空字符串
            if(empty($content)){
                $_POST['content'] = ' ';
            }
        }
        return true;
    }

}
