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

class Comment extends Admin {

    //恶意词
    public function goodkey(){
        $keyword = input('keyword');
        if ($keyword) {
            $map['name'] = array('like', '%' . (string)$keyword . '%');
        }
        
        $map['is_delete'] = 0;
        $status = input('status');
    
        if (!empty($status)) {
            $map['status'] = array('eq', $status-1);
            $this->assign('status',$status);
        } 
      
        $map['type'] = 2;
        $list = $this->lists('Goodkey', $map, 'id desc');
        foreach ($list as &$val) {
            $val['addtime'] = date('Y-m-d H:i',$val['addtime']);
        }
     
        $this->assign('_list', $list);
        $this->assign('meta_title', '恶意词管理');
        return $this->fetch();
    }
    public function addgoodkey(){
        $model=Db::name('Goodkey');
        $id=input('id');
        if(request()->isPost()){
            $id=$_POST['id'];
            $data=$_POST;
          
           

            if($id){//如果存在 就是修改
                $map['id']=array('eq',$id);
                $up=$model->where($map)->update($data);

                
            }else{  //添加
                $data['addtime']=time();
                
                $data['type']=2;
                $id=$model->insertGetId($data);
                if($id){
                   
                }
            }
            action_log('updata_goodkey','goodkey',$ids,UID);//记录行为
            $this->success('操作成功','goodkey');
        }

        if($id){
            $item=$model->where('id',$id)->find();
            if($item['addtime']){
				$item['addtime']=date("Y-m-d H:i:s",$item['addtime']);
			}
            $option=Db::name("Good_option")->where("goodid",$id)->select();
            $this->assign('item',$item);
            $this->assign('meta_title','编辑恶意词');
        }else{
            $this->assign('meta_title','添加恶意词');
        }
     
        return $this->fetch();
    }
    
    //商品列表
    public function index(){
        $gid=input('gid');
        $uid=input('uid');
      
        $map['a.id'] =['GT',0];
        if($gid){
            $map['a.gid'] =['eq',$gid];
           
            $this->assign("gid",$gid);
        }
        if($uid){
            $map['a.uid'] =['eq',$uid];
            $this->assign("uid",$uid);
        }
        $total        =    Db::name('comment')
                            ->alias('a')
                            ->join('zy_good g','a.gid = g.id')
                            ->join('zy_supuser s','a.sid = s.sid')
                            ->field('a.id,a.gid,a.content,a.score,a.pics,a.addtime,s.shopname,s.sid,g.title,g.photo_x,g.price')
                            ->where($map)
                            ->count();
       
        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = config('list_rows') > 0 ? config('list_rows') : 10;
        }
        // 分页查询
        $list = Db::name('comment')
                ->alias('a')
                ->join('zy_good g','a.gid = g.id')
                ->join('zy_supuser s','a.sid = s.sid')
                ->field('a.id,a.gid,a.content,a.score,a.pics,a.addtime,s.shopname,s.sid,g.title,g.photo_x,g.price')
                ->where($map)
                ->paginate($listRows,false,['query'=>request()->param()]);
               
        // 获取分页显示
        $page = $list->render();
       
        $this->assign('_page', $page);
        $this->assign('_total',$total);
        if($list && !is_array($list)){
            $list=$list->toArray();
        }
      
        $comment= $list['data'];
       
        $this->assign('_list', $comment);
        $this->assign('meta_title', '评价管理');
        
        return $this->fetch();
    }
    /*
     *
     *商品【添加、编辑】
     */
    public function addgoods(){
        $model=Db::name('Good');
        $id=input('id');
        if(request()->isPost()){
            $ids=$_POST['id'];
            $data=$_POST;
            $data['sort']=intval($data['sort']);//排序
            if ($_FILES['mypic']['name']){
                $path=$this->upfile('mypic');
                $data['photo_x']='/upload/images/'.$path;
            }
            if($data['pc_src']){
                $data['photo_string']=implode(",", $data['pc_src']);
                unset($data['pc_src']);
            }
            $rebate=$_POST['rebate'];
            // $data['groupendtime']=strtotime($data['groupendtime']);
            unset($data['rebate']);

            if($ids){//如果存在 就是修改
                $map['id']=array('in',$ids);
                $up=$model->where($map)->update($data);
                if($rebate){
                    foreach($rebate as $v){
                        $v["total"]=intval($v["total"]);
                        $option_id=$v['option_id'];
                        if($option_id){
                            unset($v['option_id']);
                            Db::name("Good_option")->where(array("id"=>$option_id))->update($v);
                        }else{
                            $v["goodid"]=$ids;
                            Db::name("Good_option")->insert($v);
                        }   
                    }
                }
                
            }else{  //添加
                $data['addtime']=time();
                $ids=$model->insertGetId($data);
                if($ids){
                    if($rebate){
                        foreach($rebate as $v){
                            $v["goodid"]=$ids;
                            $v["total"]=intval($v["total"]);
                            Db::name("Good_option")->insert($v);
                        }
                    }
                }
            }
            action_log('updata_good','good',$ids,UID);//记录行为
            $this->success('操作成功','index');
        }

        if($id){
            $item=$model->where('id',$id)->find();
            $catname = Db::name('category')->where(['is_delete'=>0,'id'=>$item['catid']])->find();
            $item['categorypid']=$catname['pid'];
            if($item['photo_string']){
                $photo_string=explode(",", $item['photo_string']);
            }
            if($item['groupendtime']){
				$item['groupendtime']=date("Y-m-d H:i:s",$item['groupendtime']);
			}
            $option=Db::name("Good_option")->where("goodid",$id)->select();
            $this->assign('item',$item);
            $this->assign('photo_string',$photo_string);
            $this->assign('option',$option);
            $this->assign('meta_title','编辑商品');
        }else{
            $this->assign('meta_title','添加商品');
        }
        $category = Db::name('category')->where(['is_delete'=>0,'pid'=>0])->select();
       
        $this->assign("category",$category);
        return $this->fetch();
    }
    //删除规格
    public function deloption(){
        $option_id=input('option_id');
        if($option_id){
            Db::name("Good_option")->where(['id'=>$option_id])->delete();
        }
        return true;
    }
    
    /*
     *
     * 分类管理
     * */
    public function cate(){

        $map['is_delete']=0;
        $map['pid']=input('pid')?input('pid'):0;
        $_list=$this->lists('Category',$map,'id asc');
        /**foreach ($_list as &$v) {
            $children      = Db::name("Category")->where(['pid' => $v['id'], 'is_delete' => 0])->order("id asc")->select();
            $v['children'] = $children;
        }*/
        $this->assign('_list',$_list);
        $this->assign('pid',$map['pid']);
        $this->assign('meta_title','分类管理') ;
        return $this->fetch();
    }
    public function on_cate(){

        $map['is_delete']=0;
        $map['pid']=input('pid')?input('pid'):0;
        $_list=$this->lists('Category',$map,'id asc');
        /**foreach ($_list as &$v) {
            $children      = Db::name("Category")->where(['pid' => $v['id'], 'is_delete' => 0])->order("id asc")->select();
            $v['children'] = $children;
        }*/
        return json($_list);
      
    }

    /*
     *
     *分类【添加、编辑】
     */
    public function addcate(){
        $model=Db::name('Category');
        $id=input('id');
        $this->assign('id',$id);
        if(request()->isPost()){
            $ids=$_POST['id'];
            $data=$_POST;
            $data['sort']=intval($data['sort']);//排序

            if ($_FILES['file']['name']){
                $path=$this->upfile('file');
                $data['photo_x']='/upload/images/'.$path;
            }
            if($ids){//如果存在 就是修改
                $map['id']=array('in',$ids);
                $up=$model->where($map)->update($data);
            }else{  //添加
                $map['catename']=$data['catename'];
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
        }else{
            $map['is_show']=array('eq',1);
            $map['pid']=array('eq',0);

            $_list=$model->where($map)->order('sort asc,id asc')->select();
            $this->assign('_list',$_list);

            if($id){
                $category=$model->where('id',$id)->find();
                $this->assign('category',$category);
                $this->assign('meta_title','编辑分类');
            }else{
                $this->assign('meta_title','添加分类');
            }
            return $this->fetch();
        }
    }

    

    //删除分类
    public function delcate(){
        $type=input('id');
        $da['is_delete']='1';
        $res=Db::name('category')->where(array('id'=>$type))->update($da);
        if ($res){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    //优惠券管理
    public function coupon(){
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