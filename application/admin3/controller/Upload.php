<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2018/9/18
 * Time: 11:36
 */

namespace app\admin\controller;


class Upload extends Admin
{
    public function upload()
    {
        if($this->request->isPost()){
            $res['code']=1;
            $res['msg'] = '上传成功！';
            $file = $this->request->file('file');
            $info = $file->move('../public/upload/admin/');
            //halt( $info);
            if($info){
                $dd=$info->getSaveName();
                $t=str_replace('\\','/',$dd);
                $res['name'] = $info->getFilename();
                $res['filepath'] = '/upload/admin/'.$t;
            }else{
                $res['code'] = 0;
                $res['msg'] = '上传失败！'.$file->getError();
            }
            /*   var_dump($res);
               exit();*/
            return $res;

        }
    }
    public function uploadview()
    {
        if($this->request->isPost()){
            $res['code']=1;
            $res['msg'] = '上传成功！';
            $file = $this->request->file('file');
            $info = $file->move('../public/upload/uploads/');
            //halt( $info);
            if($info){
                $dd=$info->getSaveName();
                $t=str_replace('\\','/',$dd);
                $res['name'] = $info->getFilename();
                $res['filepath'] = '/upload/uploads/'.$t;
            }else{
                $res['code'] = 0;
                $res['msg'] = '上传失败！'.$file->getError();
            }
            /*   var_dump($res);
               exit();*/
            return $res;

        }
    }
}