<?php

namespace Wubin\Authentication\Controllers;

use Wubin\Authentication\Models\Permission;
use Wubin\Authentication\Models\PermissionRoute;
use Wubin\Authentication\Models\RolePermission as RolePermissionModel;
use Wubin\Authentication\Models\Route;
use Wubin\Authentication\Services\ApiResult;
use Wubin\Authentication\Services\Utils;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Response as FacadeResponse;

/**
 * @Author     : fang®
 * @Date       : 2020-03-19 09:22
 * @Description:
 */
class PermissionController extends Controller {

    protected $nameCN = '权限';

    /**
     * @Author     : fang
     * @Description:
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FacadeResponse $rp, ApiResult $rs) {
        $permission = Permission::get(['id',
            'name',
            'code',
            'parentId',
            'icon',
            'url',
            'component',
            'isNav',
            'sort']);
        $permission = Utils::std2arr($permission);
        $tree = Utils::arr2tree($permission);
        $rs->data = $tree;
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
        try {
            $params = $rq::input();
            $permission = ['name' => $params['name'],
                'code' => $params['code'],
                'url' => $params['url'],
                'icon' => $params['icon'],
                'isNav' => boolval($params['isNav']),
                'parentId' => empty($params['parentId']) ? 0 : $params['parentId'],
                'component' => $params['component'],
                'noCache' => boolval($params['noCache']),];
            if (Permission::code($permission['code'])->exists()) {
                $rs->code = $rs::CODE_ERROR;
                $rs->message = $this->getUsedMsg('编码', $permission['code']);
                return $rp::json($rs);
            }

            DB::beginTransaction();
            $permission = Permission::create($permission);
            //权限-接口 关联
            $permissionFunction = [];
            $params['functionIds'] = empty($params['functionIds']) ? [] : $params['functionIds'];
            $functionIds = Route::find($params['functionIds'])->pluck('id');
            foreach ($functionIds as $functionId) {
                $permissionFunction[] = ['permissionId' => $permission->id,
                    'routeId' => $functionId,];
            }
            //权限接口关联
            PermissionRoute::insert($permissionFunction);
            DB::commit();
            //权限和功能
            $permission = $this->_getPermissionAndFunction($permission->id);
            $rs->message = $this->getAddOkMsg();
            $rs->data = $permission;
            return $rp::json($rs);
        } catch (\Exception $e) {
            DB::rollBack();
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getAddErrorMsg() . $e->getMessage();
            return $rp::json($rs);
        }
    }

    /**
     * @Author     : fang
     * @Description:获取权限和功能
     * @param $permissionId
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|object|null
     */
    private function _getPermissionAndFunction($permissionId) {
        $permission = Permission::find($permissionId);
        $permission = Utils::std2arr($permission);
        if ($permission) {
            $functions = Route::whereIn('id',
                PermissionRoute::where('permissionId', $permissionId)->pluck('routeId'))->get('id,name');
            $functions = Utils::std2arr($functions);
            $permission['functions'] = $functions;
        }
        return $permission;
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
        try {
            $params = $rq::input();
            if (!Permission::find($params['id'])->exists()) {
                $rs->code = $rs::CODE_ERROR;
                $rs->message = $this->getNotExistMsg();
                return $rp::json($rs);
            }
            $permission = ['name' => $params['name'],
                'code' => $params['code'],
                'url' => $params['url'],
                'icon' => $params['icon'],
                'isNav' => boolval($params['isNav']),
                'parentId' => empty($params['parentId']) ? 0 : $params['parentId'],
                'component' => $params['component'],
                'noCache' => boolval($params['noCache']),];
            //unique
            if (Permission::where('code', $permission['code'])->except($params['id'])->exists()) {
                $rs->code = $rs::CODE_ERROR;
                $rs->message = $this->getUsedMsg('编码', $permission['code']);
                return $rp::json($rs);
            }
            //权限-接口 关联
            $permissionFunction = [];
            $params['functionIds'] = empty($params['functionIds']) ? [] : $params['functionIds'];
            $functionIds = Route::find($params['functionIds'])->pluck('id')->toArray();
            $dbFuncIds = PermissionRoute::where('permissionId', $params['id'])->pluck('routeId')->toArray();
            $delFuncIds = array_diff($dbFuncIds, $functionIds);
            $addFuncIds = array_diff($functionIds, $dbFuncIds);
            foreach ($addFuncIds as $functionId) {
                $permissionFunction[] = ['permissionId' => $params['id'],
                    'routeId' => $functionId,];
            }
            //权限接口关联
            PermissionRoute::where('permissionId', $params['id'])->whereIn('routeId', $delFuncIds)->delete();
            PermissionRoute::insert($permissionFunction);
            Permission::where('id', $params['id'])->update($permission);
            //权限和功能
            $permission = $this->_getPermissionAndFunction($params['id']);
            $rs->message = $this->getModifyOkMsg();
            $rs->data = $permission;
            return $rp::json($rs);
        } catch (\Exception $e) {
            DB::rollBack();
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getModifyErrorMsg() . $e->getMessage();
            return $rp::json($rs);
        }
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
        try {
            $id = $rq::input('id', 0);
            if (!Permission::find($id)->exists()) {
                $rs->code = $rs::CODE_ERROR;
                $rs->message = $this->getNotExistMsg();
                return $rp::json($rs);
            }
            DB::beginTransaction();
            $permission = Permission::find($id);
            $permission->isDelete = 1;
            $permission->save();
            RolePermissionModel::where('permissionId', $id)->update(['isDelete' => 1]);
            PermissionRoute::where('permissionId', $id)->delete();
            DB::commit();
            $rs->message = $this->getRemoveOkMsg();
            return $rp::json($rs);
        } catch (\Exception $e) {
            DB::rollBack();
            $rs->code = $rs::CODE_ERROR;
            $rs->message = '删除失败：' . $e->getMessage();
            return $rp::json($rs);
        }
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
        if (!Permission::find($id)->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getNotExistMsg();
            return $rp::json($rs);
        }
        $info = Permission::where('id', $id)->first(['id',
            'name',
            'code',
            'parentId',
            'icon',
            'url',
            'component',
            'isNav',
            'noCache']);
        $info = Utils::std2arr($info);
        if ($info) {
            $functionIds = PermissionRoute::where('permissionId', $id)->pluck('routeId')->toArray();
            $functions = Route::selectRaw('id,name')->whereIn('id', $functionIds)->get();
            $info['functions'] = Utils::std2arr($functions);
        }
        $rs->data = $info;
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:保存全部权限
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function saveTree(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        try {
            $data = $treeData = $rq::input('data');
            $data = Utils::tree2arr($data);
            foreach ($data as $k => $v) {
                if (empty($v['code'])) {
                    $data[$k]['code'] = '';
                }
            }
            //unique start
            //code
            $codeCount = array_count_values(array_column($data, 'code'));
            foreach ($codeCount as $code => $count) {
                if ($count > 1) {
                    $rs->code = $rs::CODE_ERROR;
                    $rs->data = '编码：' . $code . '重复：' . $count . '次';
                    return $rp::json($rs);
                }
            }
            //unique end
            $dbPermission = Permission::pluck('id')->toArray();
            $del = array_diff($dbPermission, array_column($data, 'id'));
            DB::beginTransaction();
            //del
            Permission::whereIn('id', $del)->update(['isDelete' => 1]);
            //save tree
            $this->_saveTree($treeData);
            //权限功能映射
            DB::commit();
            $rs->message = '保存全部权限成功';
            return $rp::json($rs);
        } catch (\Exception $e) {
            DB::rollBack();
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $e->getMessage();
            return $rp::json($rs);
        }
    }

    /**
     * @Author     : fang
     * @Description:保存树
     * @param     $tree
     * @param int $pid
     */
    private function _saveTree($tree, $pid = 0) {
        foreach ($tree as $k => $node) {
            unset($node['functionIds'], $node['functions']);
            $node['parentId'] = $pid;
            $node['sort'] = $k + 1;
            $children = isset($node['children']) ? $node['children'] : [];
            unset($node['children']);
            if (empty($node['id'])) {
                unset($node['id']);
                $cId = Permission::create($node)->id;
            } else {
                $cId = $node['id'];
                Permission::find($cId)->update($node);
            }
            $this->_saveTree($children, $cId);
        }
    }
}
