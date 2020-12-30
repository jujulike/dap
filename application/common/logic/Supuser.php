<?php
/**
 * Created by PhpStorm.
 * User: BikeVR
 * Date: 2020/10/10
 * Time: 10:12
 */

namespace app\common\logic;

use app\common\model\Supuser as Shop;

class Supuser extends Base
{
    public function getshop($where){

        $list=Shop::all($where);
        $list=$list->toArray();
        foreach ($list as $key=>$val){

            $shop[$key]['catename']=array();
            if($val['type']){
            $cate=explode(',',$val['type']);
            $d=array();
            foreach ($cate as $k=>$v){
            $d[$k]=$this->GetCateroryName($v);
            }if($d){
                    $d=array_filter($d);
                    $ss=implode(',',$d);
                }
            $shop[$key]['catename']=$ss;
            }

        }
        return $list;
    }
    public function getshopdetail($where){
        $shop=Shop::get($where)->toArray();
          if($shop['type']){
                $cate=explode(',',$shop['type']);
                $d=array();
                foreach ($cate as $k=>$v){
                    $d[$k]=$this->GetCateroryName($v);
                }
                if($d){
                    $d=array_filter($d);
                    $ss=implode(',',$d);
                }
                 $shop['catename']=$ss;
            }


        return $shop;
    }

}