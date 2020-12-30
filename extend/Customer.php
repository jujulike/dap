<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2019/10/28
 * Time: 10:05
 */

namespace app\admin\controller;

use think\Db;
class Customer extends Admin
{
    public function index(){
        $timess=input('times');
        $timess=$timess?$timess:1;
        $start_time=input('start_time');
        $finish_time=input('finish_time');
        if($start_time&&$finish_time){
            $start_time=strtotime($start_time);
            $finish_time=strtotime($finish_time);
            if($start_time>$finish_time){
                $this->error('请选择正确的时间');
            }

            if($timess=='1'){
                $map['qd_time']=array('between',array($start_time,$finish_time));
            }
            if($timess=='2'){
                $map['add_time']=array('between',array($start_time,$finish_time));
            }
            if($timess=='3'){
                $map['work_time']=array('between',array($start_time,$finish_time));
            }
			if($timess=='4'){
                $map['zd_time']=array('between',array($start_time,$finish_time));
            }
            $start_times=date('Y-m-d ',$start_time);
            $finish_times=date('Y-m-d ',$finish_time);
            $this->assign('start_time', $start_times);
            $this->assign('finish_time', $finish_times);
			
        }
		$this->assign('timess', $timess);
        $addtime=input('qdtime');
        if($addtime){
            //$addtime=$addtime.'-01';
            $map['qdtime']    =   array('like', '%'.(string)$addtime.'%');
           // $map['qdtime']    =   array('> time', $addtime);
           // var_dump($map);
          //  exit();

        }
        $onlinetime=input('onlinetime');
		
        if($onlinetime){
            $map['onlinetime']    =   array('like', '%'.(string)$onlinetime.'%');
        }
        if(input('palyent')){
            $map['palyent']=  array('like', '%'.(string)input('palyent').'%');
            $this->assign('palyent', input('palyent'));
        }
		if(input('zhuandan')){
			$zhuandan=input('zhuandan');
			if($zhuandan==1){
			$map['zhuandan']= 1;	
			}else{
			$map['zhuandan']=0;	
			}
            $this->assign('zhuandan', $zhuandan);
        }
        if(input('company')){
            $map['company | custname | custphone | custaddress | website '] = array('like', '%'.(string)input('company').'%');
        }
        if(input('title')){
            $map['title']=  array('like', '%'.(string)input('title').'%');
        }
		if(input('kfuser')){
            $map['kfuser']=  array('like', '%'.(string)input('kfuser').'%');
        }
        $map['id'] = array('>', '0');
        if(UID>6){
			$uidgroup=db('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=db('member')->where('uid',UID)->find();
			$map['kfuser']=$kfuser['nickname'];
			
		}else{
			 $member=db('member')->where('uid',UID)->find();
            $map['swdepent']= $member['nickname'];
		}	
		$this->assign('uidgroup',$uidgroup);
        }elseif(UID=='5'){
			$swdepent=input('swdepent');
			if($swdepent){
				$map['swdepent']= $swdepent;
                $this->assign('swdepent', $swdepent);
			}else{
			$map['swdepent']= array('neq','客服部');	
			}
			
		}else{
            $swdepent  =input('swdepent');
            if($swdepent){
                $map['swdepent']= $swdepent;
                $this->assign('swdepent', $swdepent);
            }
        }
        /*var_dump($map);
        exit();*/
		//$orders=array();
		$isend=input('is_end');
		if($isend){
            $map['is_end']    =   '1';
			//$map['swdepent']=array('>', '0');
			//$map['kfuser']=array('>', '0');
        }
		
        $website = $this->lists('Customer', $map,'work_time asc'); //获得列表
        $allmoth=$this->getmoth();
        
		if(UID=='5'){
		 $adminuser=db('member')->where('uid','in','8,9,10,11,12,13,5')->where('status','1')->select();	
		}else{
			$adminuser=db('member')->where('uid','in','8,9,10,11,12,13,14,5')->where('status','1')->select();
		}
		$jduser=Db::name('member')->where('uid','>','14')->where('status','1')->select();
        $this->assign('adminuser', $adminuser);
		$this->assign('jduser', $jduser);
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('adminid', UID);
        $this->assign('allmoth', $allmoth);
        $this->assign('demand', $website);
        $this->assign('meta_title', '客户列表');
        return $this->fetch();
   }
   
   public function sdcus(){
	  
        $map['is_end']    =   '1';
        if(input('company')){
            $map['company | custname | custphone | custaddress | website '] = array('like', '%'.(string)input('company').'%');
        }
        if(input('title')){
            $map['title']=  array('like', '%'.(string)input('title').'%');
        }
	   $website = $this->lists('Customer', $map,'work_time asc'); //获得列表
	   $this->assign('demand', $website);
	   $this->assign('meta_title', '死单客户列表');
        return $this->fetch();
   }
//修改部门
public function changeswd(){
	$id = array_unique((array)input('id/a',0));
	$swd=input('swd');
	$adminuser=Db::name('member')->where('uid',$swd)->where('status','1')->find();
	$d['swdepent']=$adminuser['nickname'];
	$map = array('id' => array('in', $id) );
        if(\think\Db::name('Customer')->where($map)->update($d)){
           
            $this->success('更改成功',Cookie('__forward__'));
        } else {
            $this->error('更改失败！',Cookie('__forward__'));
        }
}
//修改跟单员
public function changegdy(){
	$id = array_unique((array)input('id/a',0));
	$swd=input('swd');
	$adminuser=Db::name('member')->where('uid',$swd)->where('status','1')->find();
	$d['kfuser']=$adminuser['nickname'];
	$map = array('id' => array('in', $id) );
        if(\think\Db::name('Customer')->where($map)->update($d)){
            $this->success('更改成功',Cookie('__forward__'));
        } else {
            $this->error('更改失败！',Cookie('__forward__'));
        }
}
    public function zdindex(){
        $timess=input('times');
        $timess=$timess?$timess:1;
        $start_time=input('start_time');
        $finish_time=input('finish_time');
        if($start_time&&$finish_time){
            $start_time=strtotime($start_time);
            $finish_time=strtotime($finish_time);
            if($start_time>$finish_time){
                $this->error('请选择正确的时间');
            }
            if($timess=='1'){
                $map['zd_time']=array('between',array($start_time,$finish_time));
            }
            if($timess=='2'){
                $map['qd_time']=array('between',array($start_time,$finish_time));
            }

            $start_times=date('Y-m-d ',$start_time);
            $finish_times=date('Y-m-d ',$finish_time);
            $this->assign('start_time', $start_times);
            $this->assign('finish_time', $finish_times);
        }
        $addtime=input('addtime');
        if($addtime){
            $map['addtime']    =   array('like', '%'.(string)$addtime.'%');
            $this->assign('addtime', $addtime);
        }
        $onlinetime=input('onlinetime');
        if($onlinetime){
            $map['onlinetime']    =   array('like', '%'.(string)$onlinetime.'%');
        }
        if(input('status')){
            $map['status']=  input('status');
            $this->assign('status', input('status'));
        }
        if(input('title')){
            $map['title| referes|zdusers']=  array('like', '%'.(string)input('title').'%');
        }
        if(input('company')){
                $map['company | custname | custphone | custaddress  '] = array('like', '%'.(string)input('company').'%');
        }
        if(input('jduser')){
               $map['jduser| swdepent']=  array('like', '%'.(string)input('jduser').'%');
        }
        if(input('qiandan')){
           //$t=input('qiandan');
           if(input('qiandan')=='1'){
               $map['qiandan']='1';
               $this->assign('qiandan', '1');
           }else{
               $map['qiandan']='0';
               $this->assign('qiandan', '2');
           }
        }
		if(input('kehu')){
           //$t=input('qiandan');
           $map['kehu']=  input('kehu');
		   $this->assign('kehus', input('kehu'));
        }
        $map['id'] = array('>', '0');
        if(UID>6){
			$uidgroup=db('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=db('member')->where('uid',UID)->find();
			$map['jduser']=$kfuser['nickname'];
			
		}else{
			$member=db('member')->where('uid',UID)->find();
            $map['swdepent']= $member['nickname'];
		}
        $this->assign('uidgroup',$uidgroup);   
        }
        $website = $this->lists('customerzd', $map); //获得用户提交需求列表
        $allmoth=$this->getmoth();
        $this->assign('allmoth', $allmoth);
		$adminuser=Db::name('member')->where('uid','in','8,9,10,11,12,14')->where('status','1')->select();
		$jduser=Db::name('member')->where('uid','>','14')->where('status','1')->select();
        $this->assign('adminuser', $adminuser);
		$this->assign('jduser', $jduser);
        $this->assign('adminid', UID);
        $this->assign('demand', $website);
        $this->assign('meta_title', '客户列表');
		Cookie('__forward__',$_SERVER['REQUEST_URI']);
        return $this->fetch();
    }
	
	//转单修改部门
public function changezdswd(){
	$id = array_unique((array)input('id/a',0));
	$swd=input('swd');
	$adminuser=Db::name('member')->where('uid',$swd)->where('status','1')->find();
	$d['swdepent']=$adminuser['nickname'];
	$map = array('id' => array('in', $id) );
        if(\think\Db::name('customerzd')->where($map)->update($d)){
           
            $this->success('更改成功',Cookie('__forward__'));
        } else {
            $this->error('更改失败！',Cookie('__forward__'));
        }
}
//转单修改跟单员
public function changezdgdy(){
	$id = array_unique((array)input('id/a',0));
	$swd=input('swd');
	$adminuser=Db::name('member')->where('uid',$swd)->where('status','1')->find();
	$d['jduser']=$adminuser['nickname'];
	$map = array('id' => array('in', $id) );
        if(\think\Db::name('customerzd')->where($map)->update($d)){
            $this->success('更改成功',Cookie('__forward__'));
        } else {
            $this->error('更改失败！',Cookie('__forward__'));
        }
}
    //添加客户
    public function addpro(){
        $id=input('id');
        //$adminuser=db('member')->where('uid','>','4')->where('status','1')->select();
		$adminuser=db('member')->where('uid','in','8,9,10,11,12,13,14,5')->where('status','1')->select();
        $this->assign('adminuser', $adminuser);
		$this->assign('adminid', UID);
		$uidgroup=db('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=db('member')->where('uid',UID)->find();
		}
		$this->assign('uidgroup',$uidgroup);
        $project=db('customer')->where('id',$id)->find();
        $this->assign('project', $project);
        if (request()->isPost()) {
            $data = $_POST;
			$t['remark'] = $data['remark'];
			unset($data['remark']);
            if($data['addtime']){
                $data['add_time']=strtotime($data['addtime']);
            }
            if($data['qdtime']){
                $data['qd_time']=strtotime($data['qdtime']);
            }
            if($data['worktime']){
                $data['work_time']=strtotime($data['worktime']);
            }
           // $m['descs'] = $data['descs'];
			$data['zfctime']=time();
            $domain=trim($data['website'],'https:');
            $domain=trim($domain,'http:');
            $domain=trim($domain,'/');
            $domain=trim($domain,'www.');

            if(!empty($domain)){
                if($data['work_time']>time()){
                    $m['if_close'] = 0;
                    $api_url='http://'.$domain.'/index.php?web_ajax=pmhs&op_type=yes';
                }else{
                    $m['if_close'] = 1;
                    $api_url='http://'.$domain.'/index.php?web_ajax=pmhs&op_type=on';
                }
            }


            //unset($data['descs']);
            if($data['id']){
				$oldproject=db('customer')->where('id',$data['id'])->find();
				if((!$oldproject['zhuandan'])&&$data['zhaundan']){
					$data['zd_time']=time();
				}
                $adds = db('customer')->where('id',$data['id'])->update($data);
                if ($adds) {
                    if(!empty($domain)) {
                        $this->getCurlObject($api_url);
                    }
					if($t['remark']){
				 $t['cusid']=$data['id']?$data['id']:$adds;
                $t['adminid']=UID;
                $t['addtime']=time();
               Db::name('customermark')->insert($t);
					}
                    $this->success("修改成功", Cookie('__forward__'));
                }
                $this->error("修改失败");
            }
            else{
				//$o['company']=$data['company'];
			//$old=db('customer')->where($o)->find();
			//if($old){
			//$this->error("添加失败，请确认该信息是否是重复信息。");	
			//}
			   $data['theaddtime']=time();
			   if($data['zhuandan']){
				$data['zd_time']=time();   
			   }
               $adds = db('customer')->insertGetId($data);
               if ($adds) {
                   if(!empty($domain)) {
                       $this->getCurlObject($api_url);
                   }
				   if($t['remark']){
				 $t['cusid']=$data['id']?$data['id']:$adds;
                $t['adminid']=UID;
                $t['addtime']=time();
               Db::name('customermark')->insert($t);
					}
                   $this->success("添加成功", 'customer/index');
               }
               $this->error("添加失败");
           }

        }
        if($id){
            $this->assign('meta_title', '修改客户');
        }else{
            $this->assign('meta_title', '添加客户');
        }

        return $this->fetch();
    }

    /*
     * 快照时间
     * */
    public function getCurlObject($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    public function addzd(){

        if (request()->isPost()) {
            $data = $_POST;
            $m['descs'] = $data['descs'];
            unset($data['descs']);
            /*$old = db('website')->where($m)->find();
            if ($old) {
                $this->error("已存在，请勿重复添加");
            }*/
            if($data['qdtime']){
                $data['qd_time'] =strtotime($data['qdtime']);
            }
            if($data['zdtime']){
                $data['zd_time'] =strtotime($data['zdtime']);
            }
			//$o['custphone']=$data['custphone'];
			//$old=db('customerzd')->where($o)->find();
			//if($old){
			//$this->error("添加失败，请确认该信息是否是重复信息。");	
			//}
            $adds = db('customerzd')->insertGetId($data);
            if ($adds) {
                $old = db('customer')->where('company',$data['company'])->find();
                if ((!$old) && (isset($data['qdtime'])) ) {
                    $cus['company']=$data['company'];
                    $cus['qdtime']=$data['qdtime'];
                    $cus['qd_time']=strtotime($data['qdtime']);
                    $cus['custname']=$data['custname'];
                    $cus['custphone']=$data['custphone'];
                    $cus['custaddress']=$data['custaddress'];
                    $cus['zhuandan']='1';
                    if(UID !='1'){
                        $adminuser=db('member')->where('uid',UID)->find();
                        $cus['swdepent']=$adminuser['nickname'];
                    }
                  //  $adds = db('customer')->insertGetId($cus);
                }
				if(!$m['descs']){
					$m['descs']='首次添加';
				}
                $m['zdid']=$data['id']?$data['id']:$adds;
                $m['adminid']=UID;
                $m['addtime']=time();
               Db::name('customerzdmark')->insert($m);
                $this->success("添加成功", 'customer/zdindex');
            }
          //  $this->error("添加失败");
        }
		//$adminuser=db('member')->where('uid','>','4')->where('status','1')->select();
		$adminuser=Db::name('member')->where('uid','in','8,9,10,11,12,13,14,5')->where('status','1')->select();
        $this->assign('adminuser', $adminuser);
		$uidgroup=Db::name('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=Db::name('member')->where('uid',UID)->find();
		}
		$this->assign('uidgroup',$uidgroup);
        $this->assign('meta_title', '添加转单客户');
        return $this->fetch();
    }
    /*//修改项目
    public function editpro(){
        if (request()->isPost()) {
            $data = $_POST;
            $m['descs'] = $data['descs'];
            unset($data['descs']);

            $adds = db('project')->where('id',$data['id'])->update($data);
            if ($adds) {
                $m['proid']=$data['id'];
                $m['adminid']=UID;
                $m['addtime']=time();
                db('projectmark')->insert($m);
                $this->success("修改成功", 'project/index');
            }
            $this->error("修改失败");
        }
    $id=input('id');
    $project=db('project')->where('id',$id)->find();
        $this->assign('project', $project);

        $this->assign('meta_title', '修改客户信息');
        return $this->fetch();
    }*/
    public function editzd(){

        if (request()->isPost()) {
            $data = $_POST;
            $m['descs'] = $data['descs'];
            unset($data['descs']);
            if($data['qdtime']){
                $data['qd_time'] =strtotime($data['qdtime']);
            }
            if($data['zdtime']){
                $data['zd_time'] =strtotime($data['zdtime']);
            }
            $adds = db('customerzd')->where('id',$data['id'])->update($data);
            if( $m['descs']){
                $m['zdid']=$data['id'];
                $m['adminid']=UID;
                $m['addtime']=time();
                db('customerzdmark')->insert($m);
            }
            $old = db('customer')->where('company',$data['company'])->find();
            if ($data['qdtime']&&(!$old)) {
                $cus['company']=$data['company'];
                $cus['qdtime']=$data['qdtime'];
                $cus['qd_time']=strtotime($data['qdtime']);
                $cus['custname']=$data['custname'];
                $cus['custphone']=$data['custphone'];
                $cus['custaddress']=$data['custaddress'];
                $cus['zhuandan']='1';
                if(UID !='1'){
                    $adminuser=db('member')->where('uid',UID)->find();
                    $cus['swdepent']=$adminuser['nickname'];
                }
                //$adds = db('customer')->insertGetId($cus);
            }
            //if ($adds) {
                $this->success("操作成功", Cookie('__forward__'));
           // }
            //$this->error("操作失败");
        }
        $id=input('id');
        $project=Db::name('customerzd')->where('id',$id)->find();
        $this->assign('project', $project);
        //$adminuser=db('member')->where('uid','>','4')->where('status','1')->select();
		$adminuser=Db::name('member')->where('uid','in','8,9,10,11,12,13,14,5')->where('status','1')->select();
        $this->assign('adminuser', $adminuser);
		$uidgroup=Db::name('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=Db::name('member')->where('uid',UID)->find();
		}
		$this->assign('uidgroup',$uidgroup);
        $this->assign('meta_title', '修改转单客户信息');
        return $this->fetch();
    }

    //项目详情
    public function prodetail(){
        $id=input('id');
        $project=Db::name('customer')->where('id',$id)->find();
        $this->assign('project', $project);
        $marks=Db::name('customermark')->where('cusid',$id)->order('id desc')->select();
        foreach ($marks as $key=>$val) {
            $adminuser=Db::name('member')->where('uid',$val['adminid'])->find();
            $marks[$key]['adminuser']=$adminuser['nickname'];
			$marks[$key]['descs']=$val['remark'];
        }
        $this->assign('marks', $marks);
       // $this->assign('marks', $marks);
        $this->assign('meta_title', '客户详情');
        return $this->fetch();
    }
    public function zdprodetail(){
        $id=input('id');
        $project=Db::name('customerzd')->where('id',$id)->find();
        $this->assign('project', $project);
        $marks=Db::name('customerzdmark')->where('zdid',$id)->order('id desc')->select();
        foreach ($marks as $key=>$val) {
            $adminuser=Db::name('member')->where('uid',$val['adminid'])->find();
            $marks[$key]['adminuser']=$adminuser['nickname'];
        }
        $this->assign('marks', $marks);
        $this->assign('meta_title', '转单客户详情');
        return $this->fetch();
    }
    //下拉框修改
    public function changepro(){
        if(input('tag')){
           $data['tag']= input('tag');
           $t['descs']='将标签改为'.input('tag');
        }
        if(input('status')){
            $data['status']= input('status');
            $t['descs']='将状态改为'.input('status');
        }
        $data['id']=input('id');
        $t['addtime']=time();
        $t['proid']=input('id');
        $t['adminid']=UID;
        $t1=db('project')->where('id',$data['id'])->update($data);
        $ts=db('projectmark')->insert($t);

          $this->success("操作成功");

    }
   //导入项目
    public function extpro(){

    }
  

    //导入excel数据进数据库
    public function expinexcel()
    {
        $file = request()->file('file');
        if (!$file) {
            $this->error('没有文件上传');
        }
        $info = $file->validate(['size' => 15678000000, 'ext' => 'xlsx,xls,csv'])
            ->move(ROOT_PATH . 'public' . DS . 'excel');
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
            $data = $this->import_excel($file_name);
			$zfc=0;
            $zfc2=0;
            $zfc3=0;
            foreach ($data as $key => $v) {
                $t[$key]['title'] = $v['0'] ? $v['0'] : '无';//合同编号
                if($v['1']){
                  $a=explode(".", $v['1']);
                  $b=$a['0'].'-'.$a['1'].'-'.$a['2'];
                  $c=strtotime(date($b));
                }else{
                    $b="无";
                    $c="0";
                }
                $t[$key]['addtime'] = $b;//下单时间
                $t[$key]['add_time'] = $c;//下单时间
                $t[$key]['company'] = $v['2'] ? $v['2'] : '无';//公司名称
                $t[$key]['palyent'] = $v['3'] ? $v['3'] : '无';//合作项目
                $t[$key]['custname'] = $v['4'] ? $v['4'] : '无';//联系人
                $t[$key]['custphone'] = $v['5'] ? $v['5'] : '无';//联系方式
                $t[$key]['website'] = $v['6'] ? $v['6'] : '无';//域名（客户域名、公司域名）
				$t[$key]['is_company'] = $v['7']=='是' ?1: 2;;//域名（客户域名2/公司域名1）
                $t[$key]['custaddress'] = $v['8'] ? $v['8'] : '无';//地址
                $t[$key]['totalmoney'] = $v['9'] ? $v['9'] : '无';//总金额
                $t[$key]['money'] = $v['10'] ? $v['10'] : '无';//首交金额
                $t[$key]['kfuser'] = $v['11'] ? $v['11'] : '无';//跟进员
                if($v['12']){
                    $a1=explode(".", $v['12']);
                    $b1=$a1['0'].'-'.$a1['1'].'-'.$a1['2'];
                    $c1=strtotime(date($b1));
                }else{
                    $b1="无";
                    $c1="0";
                }
                $t[$key]['qdtime'] = $b1;//合同签订日期
                $t[$key]['qd_time'] = $c1;//合同签订日期
                if($v['13']){
                    $a2=explode(".", $v['13']);
                    $b2=$a2['0'].'-'.$a2['1'].'-'.$a2['2'];
                    $c2=strtotime(date($b2));
                }else{
                    $b2="无";
                    $c2="0";
                }
                $t[$key]['worktime'] =  $b2;//合同终止时间
                $t[$key]['work_time'] =  $c2;//合同终止时间
                $t[$key]['xufeimoney'] = $v['14']? $v['14'] : '无';//续费金额
               
                $t[$key]['xufei'] = 1;//是否续费
                $t[$key]['zhuandan'] = $v['15']=='是' ?  1: 0;//是否转单
                $t[$key]['htmanage'] = $v['16']=='是' ? 1: 0;//合同提交
				$t[$key]['mark'] = $v['17'] ? $v['17'] : '备注';//备注
                $t[$key]['swdepent'] = $v['18'] ? $v['18'] : '无';//部门
				$t[$key]['theaddtime'] = time();//添加时间
				$t[$key]['is_ecxcel'] = 1;//exce导入
				//$b = db('customer')->where('custphone', $t[$key]['custphone'])->find();
                //if (!$b) {
                    $res = Db::name('customer')->insert($t[$key]);
                    if($res){
                        $zfc++;
                    }else{
                        $zfc2++;
                    }
               // }else{
                   // $zfc3++;
                //}
            }
            $this->success('导入成功,共导入'.$zfc.'条数据。其中'.$zfc2.'条数据失败。');//原数据
        } else {
            die('文件错误');
        }
    }
    //导入excel数据进数据库
    public function expinexcelzd()
    {
        $file = request()->file('file');
        if (!$file) {
            $this->error('没有文件上传');
        }
        $info = $file->validate(['size' => 15678000000, 'ext' => 'xlsx,xls,csv'])
            ->move(ROOT_PATH . 'public' . DS . 'excel');
        if ($info) {
            $exclePath = $info->getSaveName();  //获取文件名
            $file_name = ROOT_PATH . 'public' . DS . 'excel' . DS . $exclePath;   //上传文件的地址
            $data = $this->import_excel($file_name);
			
            foreach ($data as $key => $v) {
                $t[$key]['title'] = $v['0'] ? $v['0'] : '无';//关键词
                $t[$key]['referes'] = $v['1'] ? $v['1'] : '无';//来源
                $t[$key]['zdtime'] = $v['2'] ? $v['2'] : '无';//转单时间
                $t[$key]['company'] = $v['3'] ? $v['3'] : '无';//公司名称
                $t[$key]['custname'] = $v['4'] ? $v['4'] : '无';//联系人
                $t[$key]['custphone'] = $v['5'] ? $v['5'] : '无';//联系方式
                $t[$key]['custaddress'] = $v['6'] ? $v['6'] : '无';//地址
                $t[$key]['qduser'] = $v['7'] ? $v['7'] : '无';//接单人员
                $t[$key]['sees'] = $v['8']=='是' ? 1: 0;//是否约见
                $t[$key]['qiandan'] = $v['9']=='是' ?  1: 0;//是否签单
                $t[$key]['qdmoney'] = $v['10'] ? $v['10'] : '无';//签单金额
                $t[$key]['addtime'] = $v['11'] ? $v['11'] : '无';//签单时间
                $b = db('customerzd')->where('company', $t[$key]['company'])->find();
                if (!$b) {
                    $res = Db::name('customerzd')->insert($t[$key]);
                }
            }
            $this->success('导入成功');//原数据
        } else {
            die('文件错误');
        }
    }
    /**
     * 导入excel文件
     * @param  string $file excel文件路径
     * @return array        excel文件内容数组
     */
    function import_excel($file)
    {
        // 判断文件是什么格式
        $type = pathinfo($file);
        $type = strtolower($type["extension"]);
        if ($type == 'xlsx') {
            $type = 'Excel2007';
        } elseif ($type == 'xls') {
            $type = 'Excel5';
        }
        ini_set('max_execution_time', '0');
        // Vendor('PHPExcel.PHPExcel');
        import('PHPExcels.PHPExcel', EXTEND_PATH);
        //$objPHPExcel = new \PHPExcel();

        $objReader = \PHPExcel_IOFactory::createReader($type);
        $objPHPExcel = $objReader->load($file);
        $sheet = $objPHPExcel->getSheet(0);
        // 取得总行数
        $highestRow = $sheet->getHighestRow();
        // 取得总列数
        $highestColumn = $sheet->getHighestColumn();
        //总列数转换成数字
        $numHighestColum = \PHPExcel_Cell::columnIndexFromString("$highestColumn");
        //循环读取excel文件,读取一条,插入一条
        $data = array();
        //从第一行开始读取数据
        for ($j = 2; $j <= $highestRow; $j++) {
            //从A列读取数据
            for ($k = 0; $k < $numHighestColum; $k++) {
                //数字列转换成字母
                $columnIndex = \PHPExcel_Cell::stringFromColumnIndex($k);
                // 读取单元格
				$cell = $objPHPExcel->getActiveSheet()->getCell("$columnIndex$j")->getValue();
                //$cell = $objPHPExcel->getActiveSheet()->getCell('C2')->getValue();
				// 开始格式化
                if(is_object($cell)){
					$cell= $cell->__toString();
					}
                $data[$j][]=$cell;
                //$data[$j][] = $objPHPExcel->getActiveSheet()->getCell("$columnIndex$j")->getValue();
            }
        }
        return $data;
    }
    public function xls(){
		 $timess=input('times');
        $timess=$timess?$timess:1;
        $start_time=input('start_time');
        $finish_time=input('finish_time');
        if($start_time&&$finish_time){
            $start_time=strtotime($start_time);
            $finish_time=strtotime($finish_time);
            if($start_time>$finish_time){
                $this->error('请选择正确的时间');
            }

            if($timess=='1'){
                $map['qd_time']=array('between',array($start_time,$finish_time));
            }
            if($timess=='2'){
                $map['add_time']=array('between',array($start_time,$finish_time));
            }
            if($timess=='3'){
                $map['work_time']=array('between',array($start_time,$finish_time));
            }
			if($timess=='4'){
                $map['zd_time']=array('between',array($start_time,$finish_time));
            }
            $start_times=date('Y-m-d ',$start_time);
            $finish_times=date('Y-m-d ',$finish_time);
            $this->assign('start_time', $start_times);
            $this->assign('finish_time', $finish_times);
			
        }
		$this->assign('timess', $timess);
		$isend=input('is_end');
		if($isend){
            $map['is_end']    =   '1';
        }
        $addtime=input('qdtime');
        if($addtime){
            //$addtime=$addtime.'-01';
            $map['qdtime']    =   array('like', '%'.(string)$addtime.'%');
           // $map['qdtime']    =   array('> time', $addtime);
           // var_dump($map);
          //  exit();

        }
        $onlinetime=input('onlinetime');
        if($onlinetime){
            $map['onlinetime']    =   array('like', '%'.(string)$onlinetime.'%');
        }
        if(input('palyent')){
            $map['palyent']=  array('like', '%'.(string)input('palyent').'%');
            $this->assign('palyent', input('palyent'));
        }
		if(input('zhuandan')){
			$zhuandan=input('zhuandan');
			if($zhuandan==1){
			$map['zhuandan']= 1;	
			}else{
			$map['zhuandan']=0;	
			}
            $this->assign('zhuandan', $zhuandan);
        }
        if(input('company')){
            $map['company | custname | custphone | custaddress | website '] = array('like', '%'.(string)input('company').'%');
        }
        if(input('title')){
            $map['title']=  array('like', '%'.(string)input('title').'%');
        }
		if(input('kfuser')){
            $map['kfuser']=  array('like', '%'.(string)input('kfuser').'%');
        }
        $map['id'] = array('>', '0');
        if(UID>6){
			$uidgroup=Db::name('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=Db::name('member')->where('uid',UID)->find();
			$map['kfuser']=$kfuser['nickname'];
			
		}else{
			 $member=Db::name('member')->where('uid',UID)->find();
            $map['swdepent']= $member['nickname'];
		}	
		$this->assign('uidgroup',$uidgroup);
        }elseif(UID=='5'){
			$swdepent=input('swdepent');
			if($swdepent){
				$map['swdepent']= $swdepent;
                $this->assign('swdepent', $swdepent);
			}else{
			$map['swdepent']= array('neq','客服部');	
			}
			
		}else{
            $swdepent  =input('swdepent');
            if($swdepent){
                $map['swdepent']= $swdepent;
                $this->assign('swdepent', $swdepent);
            }
        }
        $data=Db::name('customer')->where($map)->select();
        $t=array('id','合同编号','合同签订日期','下单时间','公司名称','联系人','联系方式','地址','合作项目','测试域名','是否公司域名','域名','上线时间','总签单金额','首交金额','合同时间','跟进员','部门','是否续费','是否转单','转单时间','合同是否提交','备注','续费金额','签单时间','添加时间','有效时间','导入时间','是否导入','是否关闭','是否死单','其他');
        array_unshift($data,$t);
        $this->create_xls($data,'客户.xls');
    }
    public function zdxls(){
		$timess=input('times');
        $timess=$timess?$timess:1;
        $start_time=input('start_time');
        $finish_time=input('finish_time');
        if($start_time&&$finish_time){
            $start_time=strtotime($start_time);
            $finish_time=strtotime($finish_time);
            if($start_time>$finish_time){
                $this->error('请选择正确的时间');
            }
            if($timess=='1'){
                $map['zd_time']=array('between',array($start_time,$finish_time));
            }
            if($timess=='2'){
                $map['qd_time']=array('between',array($start_time,$finish_time));
            }

            $start_times=date('Y-m-d ',$start_time);
            $finish_times=date('Y-m-d ',$finish_time);
            $this->assign('start_time', $start_times);
            $this->assign('finish_time', $finish_times);
        }
        $addtime=input('addtime');
        if($addtime){
            $map['addtime']    =   array('like', '%'.(string)$addtime.'%');
            $this->assign('addtime', $addtime);
        }
        $onlinetime=input('onlinetime');
        if($onlinetime){
            $map['onlinetime']    =   array('like', '%'.(string)$onlinetime.'%');
        }
        if(input('status')){
            $map['status']=  input('status');
            $this->assign('status', input('status'));
        }
        if(input('title')){
            $map['title| referes|zdusers']=  array('like', '%'.(string)input('title').'%');
        }
        if(input('company')){
                $map['company | custname | custphone | custaddress  '] = array('like', '%'.(string)input('company').'%');
        }
        if(input('jduser')){
               $map['jduser| swdepent']=  array('like', '%'.(string)input('jduser').'%');
        }
        if(input('qiandan')){
           //$t=input('qiandan');
           if(input('qiandan')=='1'){
               $map['qiandan']='1';
               $this->assign('qiandan', '1');
           }else{
               $map['qiandan']='0';
               $this->assign('qiandan', '2');
           }
        }
		if(input('kehu')){
           //$t=input('qiandan');
           $map['kehu']=  input('kehu');
		   $this->assign('kehus', input('kehu'));
        }
        $map['id'] = array('>', '0');
        if(UID>6){
			$uidgroup=Db::name('auth_group_access')->where('uid',UID)->find();
		if($uidgroup['group_id']==8){
			$kfuser=Db::name('member')->where('uid',UID)->find();
			$map['jduser']=$kfuser['nickname'];
			
		}else{
			$member=Db::name('member')->where('uid',UID)->find();
            $map['swdepent']= $member['nickname'];
		}
        $this->assign('uidgroup',$uidgroup);   
        }
        $data=Db::name('customerzd')->where($map)->select();
        $t=array('id','关键词','来源','下单时间','公司名称','签单金额','联系人','联系方式','地址','接单员','是否约见','是否签单','签单时间','备注','客户类型','部门','转单员');
        array_unshift($data,$t);
        $this->create_xls($data,'转单客户.xls');
    }
    function create_xls($data,$filename='simple.xls'){
        ini_set('max_execution_time', '0');
        //Vendor('PHPExcel.PHPExcel');
        import('PHPExcels.PHPExcel', EXTEND_PATH);
        $filename=str_replace('.xls', '', $filename).'.xls';
        $phpexcel = new \PHPExcel();
        $phpexcel->getProperties()
            ->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
        $phpexcel->getActiveSheet()->fromArray($data);
        $phpexcel->getActiveSheet()->setTitle('Sheet1');
        $phpexcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        $objwriter = \PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
        $objwriter->save('php://output');
        exit;
    }
    public function getmoth(){
        $startdate = date("Y-m-d",strtotime("-1 year"));

        $enddate = date("Y-m-d",time());

        $s = strtotime($startdate);
        $e = strtotime($enddate);

        $num = (date('Y',$e)-date('Y',$s)-1)*12+(12-date('m',$s)+1)+date('m',$e);

        $months = array();
        for($i=0; $i<$num; $i++){
            $d = mktime(0,0,0,date('m',$s)+$i,date('d',$s),date('Y',$s));
            $months[] = date('Y-m',$d);
        }

        return $months;
    }
	/**
     * 更改成为死单
     * Author: zfc000000
     */
    public function chanagesd(){
        $id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
		$d['is_end']='1';
        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Customer')->where($map)->update($d)){
           
            $this->success('更改成功');
        } else {
            $this->error('更改失败！');
        }
    }
	public function chanagesd2(){
        $id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
		$d['is_end']='2';
        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Customer')->where($map)->update($d)){
           
            $this->success('更改成功');
        } else {
            $this->error('更改失败！');
        }
    }
	public function delcust(){
		$id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            $this->error('请选择要删除的数据!');
        }
        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Customer')->where($map)->delete()){
           
            $this->success('更改成功');
        } else {
            $this->error('更改失败！');
        }
        
	}
	public function delpro(){
       $id = array_unique((array)input('id/a',0));
        if ( empty($id) ) {
            $this->error('请选择要删除的数据!');
        }
		$map = array('id' => array('in', $id) );
		$map2 = array('zdid' => array('in', $id) );
        $project=Db::name('customerzd')->where($map)->delete();
        $marks=Db::name('customerzdmark')->where($map2)->delete();
            $this->success("删除成功");

    }
}