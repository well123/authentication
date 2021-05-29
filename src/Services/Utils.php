<?php /** @noinspection Annotator */

namespace Wubin\Authentication\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @Author     : fang
 * @Date       : 2020-03-19 09:37
 * @Description:
 */
class Utils {

    /**
     * @Author     : fang
     * @Description:array to tree
     * @param $arr
     * @return array
     */
    public static function arr2tree($arr) {
        if (empty($arr)) {
            return [];
        }
        $new = [];
        foreach ($arr as $item) {
            $new[$item['parentId']][] = $item;
        }
        return self::_arr2tree($new);
    }

    /**
     * @Author     : fang
     * @Description:array to tree
     * @param     $arr
     * @param int $pid
     * @return array
     */
    private static function _arr2tree($arr, $pid = 0) {
        $tree = [];
        foreach ($arr as $parentId => $list) {
            //排序
            $tmpSort = array_column($list, 'sort');
            if ($tmpSort) {
                array_multisort($tmpSort, SORT_ASC, $list);
            }
            //子树
            if ($parentId == $pid) {
                foreach ($list as $item) {
                    //$item['children'] = [];
                    if (isset($arr[$item['id']])) {
                        $item['children'] = self::_arr2tree($arr, $item['id']);
                    }
                    $tree[] = $item;
                }
            }
        }
        return $tree;
    }

    /**
     * @Author     : fang
     * @Description:tree to arr
     * @param $tree
     * @return array
     */
    public static function tree2arr($tree) {
        if (empty($tree)) {
            return [];
        }
        return self::_tree2arr($tree);
    }

    /**
     * @Author     : fang
     * @Description:
     * @param $tree
     * @return array
     */
    protected static function _tree2arr($tree) {
        $arr = [];
        foreach ($tree as $node) {
            if (!empty($node['children'])) {
                $children = $node['children'];
                unset($node['children']);
                $arr[] = $node;
                $arr = array_merge($arr, self::_tree2arr($children));
            } else {
                unset($node['children']);
                $arr[] = $node;
            }
        }
        return $arr;
    }

    /**
     * @Author     : fang
     * @Description:tree to arr 记录level,parentId
     * @param $tree
     * @param $level
     * @return array
     */
    public static function tree2arrWithLevelPid($tree, $level = 1) {
        if (empty($tree)) {
            return [];
        }
        return self::_tree2arrWithLevelPid($tree, $level, 0);
    }

    /**
     * @Author     : fang
     * @Description:
     * @param $tree
     * @param $level
     * @param $pid
     * @return array
     */
    protected static function _tree2arrWithLevelPid($tree, $level = 1, $pid = 0) {
        $arr = [];
        foreach ($tree as $node) {
            $node['level'] = $level;
            $node['parentId'] = $pid;
            if (!empty($node['children'])) {
                $children = $node['children'];
                unset($node['children']);
                $arr[] = $node;
                $arr = array_merge($arr, self::_tree2arrWithLevelPid($children, $level + 1, $node['id']));
            } else {
                unset($node['children']);
                $arr[] = $node;
            }
        }
        return $arr;
    }

    /**
     * @Author     : fang
     * @Description:sql with binding for debugging
     * @param Builder $query
     * @return string
     */
    public static function sqlWithBinding(Builder $query) {
        return vsprintf(str_replace('?', '%s', $query->toSql()),
            collect($query->getBindings())->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray());
    }

    /**
     * @Author     : fang
     * @Description:去前缀
     * @param $str
     * @param $prefix
     * @return bool|string
     */
    public static function rmPrefix($str, $prefix) {
        $len = strlen($prefix);
        if (substr($str, 0, $len) === $prefix) {
            return substr($str, $len);
        }
        return $str;
    }

    /**
     * @Author     : fang
     * @Description:文件名后缀
     * @return string
     */
    public static function fileSuffix() {
        return date('YmdHis') . '-' . rand(1000, 9999);
    }

    /**
     * @Author     : fang
     * @Description:数量
     * @param $value
     * @return bool
     */
    public static function isQuantityValid($value) {
        if (!preg_match('/^[1-9]\d*$/', $value)) {
            return false;
        }
        return true;
    }

    /**
     * @Author     : fang
     * @Description:
     * @param $value
     * @return bool
     */
    public static function isMoneyValid($value) {
        if (!preg_match('/(^[1-9]([0-9]+)?(\.[0-9]{1,2})?$)|(^(0){1}$)|(^[0-9]\.[0-9]([0-9])?$)/', $value)) {
            return false;
        }
        return true;
    }

    /**
     * @Author     : fang
     * @Description:格式化金额
     * @param     $value
     * @param int $decimals
     * @return string
     */
    public static function formatMoney($value, $decimals = 2) {
        return number_format($value, $decimals, '.', '');
    }

    /**
     * @Author     : fang
     * @Description:分割地址
     * @param $fullAddress
     * @return array
     */
    public static function splitAddress($fullAddress) {
        $tmpProvince = mb_substr($fullAddress, 0, 2);
        $province = DB::table('province')->selectRaw('name,provinceId')->where('name', 'like',
            "{$tmpProvince}%")->first();
        $province = self::std2arr($province);
        if (!empty($province)) {
            $provinceId = $province['provinceId'];
            $province = $province['name'];
            $tmpFullAddress = mb_substr($fullAddress, mb_strlen($province));
            $tmpCity = mb_substr($tmpFullAddress, 0, 2);
            $city = DB::table('city')->selectRaw('name,cityId')->where('provinceId', $provinceId)->where('name', 'like',
                "{$tmpCity}%")->first();
            $city = self::std2arr($city);
            if (!empty($city)) {
                $cityId = $city['cityId'];
                $city = $city['name'];
                $tmpFullAddress = mb_substr($tmpFullAddress, mb_strlen($city));
                $tmpCounty = mb_substr($tmpFullAddress, 0, 3);
                $county = DB::table('county')->where('cityId', $cityId)->where('name', 'like',
                    "{$tmpCounty}%")->value('name');
                if (!empty($county)) {
                    $address = mb_substr($tmpFullAddress, mb_strlen($county));
                }
            }
        }
        return ['province' => empty($province) ? '' : $province,
            'city' => empty($city) ? '' : $city,
            'county' => empty($county) ? '' : $county,
            'address' => empty($address) ? (empty($tmpFullAddress) ? $fullAddress : $tmpFullAddress) : $address,];
    }

    /**
     * @Author     : fang
     * @Description:std to arr
     * @param $std
     * @return mixed
     */
    public static function std2arr($std) {
        return json_decode(json_encode($std), true);
    }

    /**
     * @Author     : fang
     * @Description:追加子节点
     * @param $parent
     * @param $children
     * @return mixed
     */
    public static function appendChildren($parent, $children) {
        $pidChildren = [];
        foreach ($children as $c) {
            $tmp = $c;
            unset($tmp['parentId']);
            $pidChildren[$c['parentId']][] = $tmp;
        }
        foreach ($parent as $k => $p) {
            $parent[$k]['children'] = isset($pidChildren[$p['id']]) ? $pidChildren[$p['id']] : [];
        }
        return $parent;
    }

    /**
     * @Author     : fang
     * @Description:获取字段枚举['name'=>'a','name'=>'b']
     * @param $table
     * @param $field
     * @param $assocField
     * @return array
     */
    public static function getDbEnumAssoc($table, $field, $assocField = 'name') {
        $arr = [];
        $enum_arr = self::getDbEnum($table, $field);
        foreach ($enum_arr as $item) {
            $arr[] = [$assocField => $item,];
        }
        return $arr;
    }

    /**
     * @Author     : fang
     * @Description:获取字段枚举['a','b']
     * @param $table
     * @param $field
     * @return array
     */
    public static function getDbEnum($table, $field) {
        $rs = DB::select("show columns from `{$table}` where field = '{$field}'");
        $rs = self::std2arr($rs);
        if (empty($rs) || empty($rs[0]['Type'])) {
            return [];
        }
        $enum = $rs[0]['Type'];
        $enum_arr = explode("(", $enum);
        $enum = $enum_arr[1];
        $enum_arr = explode(")", $enum);
        $enum = str_replace("'", '', $enum_arr[0]);
        $enum_arr = explode(",", $enum);
        return $enum_arr;
    }

    /**
     * @Author     : fang
     * @Description:获取一页的数据
     * @param $item :全部数据
     * @param $page :当前页
     * @param $size :每页大小
     * @return array
     */
    public static function getSinglePageItems($item, $page, $size) {
        return $item = array_slice($item, ($page - 1) * $size, $size);
    }

    /**
     * @Author     : fang
     * @Description:无数据-分页
     * @return array
     */
    public static function getEmptyPagination() {
        return ['total' => 0,
            'rows' => [],];
    }

    /**
     * @Author     : fang
     * @Description:获取功能路径
     * @return bool|string
     */
    public static function getFunctionUrl() {
        $path = request()->path();
        $path = substr($path, strpos($path, '/') + 1);
        return $path;
    }

    /**
     * @Author     : fang
     * @Description:保存文件
     * @param $file
     * @param $path
     * @return array
     * @throws \Exception
     */
    public static function saveFile($file, $path = '') {
        if (is_null($file)) {
            return [];
        }
        $savePath = DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR;
        if (!empty($path)) {
            $savePath .= $path . DIRECTORY_SEPARATOR;
        }
        $fileName = Str::random(64);
        $ext = $file->getClientOriginalExtension();
        $file->storePubliclyAs($path, $fileName . '.' . $ext, 'upload');
        return ['url' => $savePath . $fileName . '.' . $ext,];
    }

    /**
     * @Author     : fang
     * @Description:去掉路径中的协议和域名
     * @param $url
     * @return mixed
     */
    public static function removeSchemeAndHttpHost($url) {
        return str_replace(request()->getSchemeAndHttpHost(), '', $url);
    }

    public static function randStr($len) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
        for (; $len >= 1; $len--) {
            $position = rand() % strlen($chars);
            $string .= substr($chars, $position, 1);
        }
        return $string;
    }

    public static function getOrderSnStr($prefix): string {
        return $prefix . date('YmdHis') . rand(10000, 99999);
    }

    public static function amountWithoutComma($amount) {
        return str_replace(',', '', $amount);
    }
}
