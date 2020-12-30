<?php
/**
 * Created by PhpStorm.
 * User: wm
 * Date: 2018/3/13
 * Time: 17:57
 */
namespace app\admin\controller;
use wxpay;
use think\Db;
/**
 * 行为控制器
 * Author: zfc000000
 */

class Coupon extends Admin {



    //优惠券管理
    public function index(){
        $map['is_delete']=0;
        $_list=$this->lists('Coupon',$map,'id desc');
        foreach($_list as &$v){
            $v['count']=Db::name("mycoupon")->where(['couponid'=>$v['id']])->count();
            $v['sycount']=Db::name("mycoupon")->where(['couponid'=>$v['id'],'sent'=>1])->count();

        }
        $this->assign('_list',$_list);
        $this->assign('meta_title','优惠券管理') ;
        return $this->fetch();
    }

    /*
     *
     *优惠券【添加、编辑】
     */
    public function addcoupon(){
        $model=Db::name('Coupon');
        $id=input('id');
        if(request()->isPost()){
            $ids=$_POST['id'];
            $data=$_POST;
            if($ids){//如果存在 就是修改
                $map['id']=array('in',$ids);
                $up=$model->where($map)->update($data);
            }else{  //添加
                $data['addtime']=time();
                $up=$ids=$model->insertGetId($data);
            }
            if($up){
                action_log('updata_coupon','coupon',$ids,UID);//记录行为
                $this->success('操作成功','coupon');
            }else{
                $this->error('操作失败');
            }
        }

        if($id){
            $coupon=$model->where('id',$id)->find();
            $this->assign('coupon',$coupon);
            $this->assign('meta_title','编辑优惠券');
        }else{
            $this->assign('meta_title','添加优惠券');
        }
        return $this->fetch();
    }

    

    //删除优惠券
    public function delcoupon(){
        $did=input('id');
        $da['is_delete']='1';
        $res=Db::name('Coupon')->where(array('id'=>$did))->update($da);
        if ($res){
            action_log('updata_coupon','coupon',$did,UID);//记录行为
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

   

}