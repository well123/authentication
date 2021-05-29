<?php

namespace Wubin\Authentication\Services;

/**
 * @Author     : fang
 * @Date       : 2020-03-18 10:29
 * @Description:
 */
class ApiResult {

    //成功
    const CODE_OK = 200;
    //未登录
    const CODE_UNAUTHORIZED = 10001;
    //失败-出错
    const CODE_ERROR = 50000;
    //code
    const PARAMS_ERROR = 20000;
    //message
    const WAREHOUSE_ERROR = 20001;
    //data
    const SHOP_ERROR = 20002;
    //token
    const ORDER_EXIST = 20003;
    const PARAMS_NEED = 20004;
    const GOODS_NOT_EXIST = 20005;
    const ORDER_NOT_EXIST = 20006;
    public $code = self::CODE_OK;
    public $message = '';
    public $data = null;
    public $token = '';
}
