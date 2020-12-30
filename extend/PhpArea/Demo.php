<?php
/*
 * @Descripttion:
 * @version:
 * @Author: zfc
 * @Date: 2020-11-23 10:38:41
 * @LastEditors: zfc
 * @LastEditTime: 2020-11-23 10:39:25
 */
class Demo
{
    public function addressIn($lng = '114.236559', $lat = '30.531549')
    {
        $area_arr = [];
        if (cache('address_area_arr')) {
            $area_arr = cache('address_area_arr');
        } else {
            $_area_id = [];
            $_area_id = Db::name('address_area')->where('delete_time is null')->column('id');
            foreach ($_area_id as $v) {
                $site_arr = [];
                $site_arr = Db::name('address_site')
                        ->field('lng x,lat y')
                        ->where('delete_time is null')
                        ->where('status=1')
                        ->where('address_area_id', $v)
                        ->select()
                        ->toArray();
                $area_arr[] = $site_arr;
            }
            cache('address_area_arr', $area_arr);
        }
        import('Area', EXTEND_PATH);
        $area = new \Area($area_arr);

        var_dump($area->checkPoint($lng, $lat));
    }
}
