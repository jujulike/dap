<?php
/**根据用户地址判断是否在五环内
1.获取用户地址经纬度
2.获取五环多边行各个顶点经纬度数组
3.判断用户地址是否在多边形内

《php百度地图api判断地址是否在多边形区域内》**/
/**
 * @desc 根据地址获取经纬度
 *
 * @param string $addr 地址
 * @param string $ak   百度api密钥
 */

namespace Phpaddress;

class PhpAddress
{
    public function GetLN($addr = ”)
    {
        $addr = urlencode($addr);
        $api_url = 'http://api.map.baidu.com/geocoder/v2/?address='.$addr.'&output=json&ak=dAWur0c6zVi7H6QsIEKsTK6vQAqWbtpG';
        $json = file_get_contents($api_url);
        $json_arr = json_decode($json);

        return $json_arr;
    }

    /**
     * @desc 根据两点间的经纬度计算距离
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000; //approximate radius of earth in meters

        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;

        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;

        return round($calculatedDistance);
    }

    // 判断点 是否在多边形 内
    public function isPointInPolygon($polygon, $lnglat)
    {
        $count = count($polygon);
        $px = $lnglat['lat'];
        $py = $lnglat['lng'];
        $flag = false;
        for ($i = 0, $j = $count - 1; $i < $count; $j = $i, $i++) {
            $sy = $polygon[$i]['lng'];
            $sx = $polygon[$i]['lat'];
            $ty = $polygon[$j]['lng'];
            $tx = $polygon[$j]['lat'];
            if ($px == $sx && $py == $sy || $px == $tx && $py == $ty) {
                return true;
            }
            if ($sy < $py && $ty >= $py || $sy >= $py && $ty < $py) {
                $x = $sx + ($py - $sy) * ($tx - $sx) / ($ty - $sy);
                if ($x == $px) {
                    return true;
                }
                if ($x > $px) {
                    $flag = !$flag;
                }
            }
        }

        return $flag;
    }
}
/*

$addr = GetLN(‘朝阳区霄云路鹏润大厦B座29层', $ak);

$polygon = [
[
“lng” => 116.497492,
“lat” => 40.026915,
],
[
“lng” => 116.288222,
“lat” => 40.030451,
],
[
“lng” => 116.223832,
“lat” => 39.997737,
],
[
“lng” => 116.211184,
“lat” => 39.859634,
],
[
“lng” => 116.283623,
“lat” => 39.773621,
],
[
“lng” => 116.420453,
“lat” => 39.762973,
],
[
“lng” => 116.565332,
“lat” => 39.853431,
],
[
“lng” => 116.554983,
“lat” => 39.945539,
],
[
“lng” => 116.499791,
“lat” => 40.025147,
],
];
$lnglat = [
“lng” => 116.47340460859,
“lat” => 39.96493228002,
];

$tt = isPointInPolygon($polygon, $lnglat);

 */
