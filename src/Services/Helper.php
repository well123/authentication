<?php

namespace Wubin\Authentication\Services;

use Wubin\Authentication\Models\Route;
use Illuminate\Support\Facades\DB;

/**
 * @Author     : fang
 * @Date       : 2020-03-18 21:27
 * @Description:
 */
class Helper {

    /**
     * @Author     : fang
     * @Description:获取用户id
     * @return mixed
     */
    public static function userId() {
        return self::userInfo()['id'];
    }

    public static function userInfo() {
        return session()->get('adminUser');
    }

    /**
     * @Author     : fang
     * @Description:获取会员id
     * @return mixed
     */
    public static function memberId() {
        return request()->get('_memberId');
    }

    /**
     * @Author     : fang
     * @Description:获取X-Cookie
     * @return array|string|null
     */
    public static function cookie() {
        return request()->header('X-Cookie');
    }

    /**
     * @Author     : fang
     * @Description:获取X-Source
     * @return array|string|null
     */
    public static function source() {
        $source = request()->header('X-Source');
        switch ($source) {
            case 'APP':
                return 'APP';
            case 'MP-WEIXIN':
                return '小程序';
        }
        return $source;
    }

    /**
     * @Author     : sky.sun
     * @Description:获取配置
     * @param $key
     * @return mixed
     */
    public static function getConfig($key) {
        $value = DB::table(Config::table)->where('key', $key)->value('value');
        return json_decode($value, true);
    }

    /**
     * @Author     : fang
     * @Description:id=>name
     * @param $table
     * @param $ids
     * @param $hasDelete
     * @return mixed
     */
    public static function idName($table, $ids, $hasDelete = true) {
        $rs = DB::table($table)->whereIn('id', $ids);
        if ($hasDelete) {
            $rs = $rs->where('isDelete', 0);
        }
        $rs = $rs->pluck('name', 'id');
        return $rs;
    }

    /**
     * @Author     : sky.sun
     * @Description: 根据支付code获取支付方式
     * @param $code
     * @return string
     */
    public static function payWay($code) {
        switch ($code) {
            case 'alipay':
                return '支付宝';
            case 'wxpay':
                return '微信';
        }
        return $code;
    }

    /**
     * @Author     : fang
     * @Description:根据地址获取经纬度
     * @param        $province
     * @param        $city
     * @param        $county
     * @param string $address
     * @return array
     */
    public static function getLatitudeAndLongitudeByFullAddress($province, $city, $county, $address = '') {
        $client = new Client();
        $result = $client->geoCoding($province . $city . $county . $address, $city);
        if ($result['status'] == 0) {
            return ['latitude' => $result['result']['location']['lat'],
                'longitude' => $result['result']['location']['lng'],];
        }
        return [];
    }

    public static function getRouteNameByUrl($url) {
        $name = Route::where('url', $url)->value('name');
        return strval($name);
    }

    public static function generateToken() {
        return strtoupper(md5(date('Y-m-d H:i:s') . self::randStr(20, ['strUp', 'strLow', 'num', 'special'])));
    }

    /**
     * @Author     : sky.sun
     * @Description: 生成随机字符串
     * @param int $num 位数
     * @param array $type strUp,strLow,num,special 包含几个数组就几个
     * @return string
     */
    public static function randStr($num, $type = []) {
        $strUpCell = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $strLowCell = 'abcdefghijklmnopqrstuvwxyz';
        $numCell = '1234567890';
        $specialCell = '~!@#$%*.?-';
        $cell = '';
        foreach ($type as $value) {
            $value .= 'Cell';
            if (isset($$value)) {
                $cell .= $$value;
            }
        }
        $length = strlen($cell);
        $str = '';
        while ($num > 0) {
            $str .= $cell[rand(0, $length - 1)];
            $num--;
        }
        return $str;
    }
}
