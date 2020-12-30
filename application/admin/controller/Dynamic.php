<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2018/9/17
 * Time: 17:31
 */

namespace app\admin\controller;

use think\Session;
class Dynamic extends Admin
{
    /*
     *
     * 景区动态
     */
    public function index(){
		$keyword = input('keyword');
        if ($keyword) {
            $map['title'] = array('like', '%' . (string)$keyword . '%');
        }
        $dynamic=$this->lists('Dynamic',$map,'id desc');
		$dy_cate=db('Dynamic_cate')->where(array('is_show'=>1,'is_delete'=>0))->select();
		$this->assign('dycate',$dy_cate);
		$this->assign('_list',$dynamic);
        $this->assign('meta_title','景区动态列表');
        return $this->fetch();
    }
	/*
	景区动态的添加和编辑
	*/
	public function add(){
		$id=input('id');
		if(request()->isPost()) {
			
			$ids=$_POST['id'];
			$_POST['addtime']=strtotime($_POST['addtime']);
			if ($_FILES['ico1']['name']){
                $path=$this->upfile('ico1');
                $_POST['pic']='/upload/images/'.$path;
            }
			
			$data=$_POST;
			if($ids){//如果存在 就是修改
				$up=db('Dynamic')->where('id',$ids)->update($data);
			}else{
				$up=db('Dynamic')->insertGetId($data);
			}
			if($up){
			   $this->success('操作成功','dynamic/index');
			}else{
				$this->error('操作失败');
			}
		}
		if($id){
			$this->assign('meta_title','编辑景区动态');
			$method=db('Dynamic')->where('id',$id)->find();
		}else{
			$method=array();
			$this->assign('meta_title','添加景区动态');
		}
		$dy_cate=db('Dynamic_cate')->where(array('is_show'=>1,'is_delete'=>0))->order('sort desc,id asc')->select();
		$this->assign('dycate',$dy_cate);
		$this->assign('article',$method);
        return $this->fetch('edit');
    }
    /**
     * 删除，批量删除
     */
    public function delm(){
        $id=input('id');
		$dbname='dynamic';
        //$dbname=input('dbname')?input('dbname'):'Dynamic';
        $res=db($dbname)->where(array('id'=>$id))->delete();
        if ($res){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
/*
     *
     * 分类管理
     * */
    public function cate(){
        $map['is_delete']=0;
        $_list=$this->lists('Dynamic_cate',$map,'id desc');
        $this->assign('_list',$_list);
        $this->assign('meta_title','景区动态分类') ;
        return $this->fetch();
    }

    /*
     *
     *分类【添加、编辑】
     */
    public function addcate(){
        $model=db('Dynamic_cate');
        $id=input('id');
        if(request()->isPost()){
            $ids=$_POST['id'];
            $data=$_POST;
            $data['sort']=intval($data['sort']);//排序

            /*if ($_FILES['file']['name']){
                $path=$this->upfile('file');
                $data['photo_x']='/upload/images/'.$path;
            }*/
			
            if($ids){//如果存在 就是修改
                $map['id']=array('in',$ids);
                $up=$model->where($map)->update($data);
            }else{  //添加
                $map['name']=$data['name'];
                $oldtype=$model->where($map)->find();
                if($oldtype){
                    $this->error('请不要重复添加');
                }
                $up=$model->insert($data);
            }
            if($up){
                $this->success('操作成功','cate');
            }else{
                $this->error('操作失败');
            }
        }

        if($id){
            $category=$model->where('id',$id)->find();
            $this->assign('category',$category);
            $this->assign('meta_title','编辑分类');
        }else{
            $this->assign('meta_title','添加分类');
        }
        return $this->fetch();
    }
	 //删除分类
    public function delcate(){
        $type=input('id');
        $da['is_delete']='1';
        $res=db('Dynamic_cate')->where(array('id'=>$type))->update($da);
        if ($res){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

}