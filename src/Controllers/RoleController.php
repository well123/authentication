<?php

namespace Wubin\Authentication\Controllers;

use Wubin\Authentication\Models\Role;
use Wubin\Authentication\Models\RolePermission as RolePermissionModel;
use Wubin\Authentication\Services\ApiResult;
use Wubin\Authentication\Services\RolePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller {

    protected $nameCN = '角色';

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
        $page = $rq::input('page', 1);
        $pageSize = $rq::input('size', 10);
        $rs->data = ['rows' => Role::exceptAdmin()->name($name)->forPage($page, $pageSize)->get(),
            'total' => Role::name($name)->exceptAdmin()->count()];
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
        $message = ['name.required' => '名称是必填项',];
        $validator = Validator::make($rq::all(), ['name' => 'required',], $message);
        if ($validator->fails()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = implode(',', $validator->getMessageBag()->all());
            return $rp::json($rs);
        }
        //name unique
        if (Role::name($name)->exceptAdmin()->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getUsedMsg('名称', $name);
            return $rp::json($rs);
        }
        $role = new Role;
        $role->name = $name;
        $role->save();
        $rs->message = $this->getAddOkMsg();
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
        $id = $rq::input('id', 0);
        $message = ['name.required' => '名称是必填项',];
        $validator = Validator::make($rq::all(), ['name' => 'required',], $message);
        if ($validator->fails()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = implode(',', $validator->getMessageBag()->all());
            return $rp::json($rs);
        }
        $role = Role::find($id);
        //name unique
        if (Role::name($name)->except($id)->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getUsedMsg('名称', $name);
            return $rp::json($rs);
        }
        $role->name = $name;
        $role->save();
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
        if (!Role::find($id)->exists()) {
            $rs->code = $rs::CODE_ERROR;
            $rs->message = $this->getNotExistMsg();
            return $rp::json($rs);
        }
        $role = Role::find($id);
        $role->isDelete = 1;
        $role->save();
        $rs->message = $this->getRemoveOkMsg();
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
        $role = Role::find($id, ['id', 'name']);
        if (empty($role)) {
            $rs->code = $rs::CODE_ERROR;
        }
        $rs->data = $role;
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:角色权限
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     */
    public function permission(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $roleId = $rq::input('roleId', 0);
        $permission = RolePermission::permissionByRoleIdTree($roleId);
        $rs->data = $permission;
        return $rp::json($rs);
    }

    /**
     * @Author     : fang
     * @Description:保存角色权限
     * @param FacadeRequest $rq
     * @param FacadeResponse $rp
     * @param ApiResult $rs
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermission(FacadeRequest $rq, FacadeResponse $rp, ApiResult $rs) {
        $params = $rq::input();
        $rolePermission = [];
        //db
        $dbPermission = RolePermissionModel::where('roleId', $params['roleId'])->pluck('permissionId')->toArray();
        $del = array_diff($dbPermission, $params['permissionIds']);
        $new = array_diff($params['permissionIds'], $dbPermission);
        if (!empty($new)) {
            foreach ($new as $permissionId) {
                $rolePermission[] = ['roleId' => $params['roleId'],
                    'permissionId' => $permissionId,];
            }
        }
        DB::beginTransaction();
        RolePermissionModel::where('roleId', $params['roleId'])->whereIn('permissionId',
            $del)->update(['isDelete' => 1]);
        RolePermissionModel::insert($rolePermission);
        DB::commit();
        $rs->message = '保存角色权限成功';
        return $rp::json($rs);
    }
}
