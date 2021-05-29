<?php

namespace Wubin\Authentication\Controllers;

use Wubin\Authentication\Models\Route;
use Wubin\Authentication\Services\ApiResult;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Response as FacadeResponse;

/**
 * @Author     : fang
 * @Date       : 2020-05-06 12:28
 * @Description:
 */
class RouteController extends Controller {

    protected $nameCN = '功能';

    /**
     * @Author     : fang
     * @Description:
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $name = $rq::input('name', '');
        $url = $rq::input('url', '');
        $rs->data = Route::name($name)->url($url)->get();
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:新增
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $name = $rq::input('name', '');
        $url = $rq::input('url', '');

        //name unique
        if (Route::name($name)->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getUsedMsg('名称', $name);
            return $rp::json($rs);
        }
        $route = new Route();
        $route->name = $name;
        $route->url = $url;
        $route->save();
        $rs->message = $this->getAddOkMsg();
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:信息
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $id = $rq::input('id', 0);
        $info = Route::find($id, ['id', 'name', 'url']);
        $rs->data = $info;
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:编辑
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function modify(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $name = $rq::input('name', '');
        $url = $rq::input('url', '');
        $id = $rq::input('id', 0);

        //name unique
        if (Route::name($name)->except($id)->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getUsedMsg('名称', $name);
            return $rp::json($rs);
        }
        $route = Route::find($id);
        $route->name = $name;
        $route->url = $url;
        $route->save();
        $rs->message = $this->getModifyOkMsg();
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:删除
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $id = $rq::input('id', 0);
        if (!Route::find($id)->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getNotExistMsg();
            return $rp::json($rs);
        }
        Route::find($id)->delete();
        $rs->message = $this->getRemoveOkMsg();
        return $rp::json($rs);
    }

    /**
     * @Author     : sky.sun
     * @Description: 批量增加功能根据接口导出的json文件
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return mixed
     */
    public function addAllByJsonFiLe(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $content = file_get_contents('./xls/1.json');
        $content = json_decode($content, true);
        $addData = [];
        foreach ($content['item'] as $item) {
            foreach ($item['item'] as $value) {
                $data = ['name' => $value['name'],
                    'url' => 'admin/' . $value['request']['url']['path'][0],];
                $route = Route::where('url', $data['url'])->first();
                if ($route) {
                    $route->name = $value['name'];
                    $route->url = 'admin/' . $value['request']['url']['path'][0];
                    $route->save();
                } else {
                    $addData[] = $data;
                }
            }
        }
        Route::insert($addData);
        $rs->message = '批量保存成功';
        return $rp::json($rs);
    }
}
