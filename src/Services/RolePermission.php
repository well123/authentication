<?php

namespace Wubin\Authentication\Services;

use Wubin\Authentication\Models\Permission;
use Wubin\Authentication\Models\RolePermission as RolePermissionModel;
use Wubin\Authentication\Services\Helper;
use Wubin\Authentication\Services\Utils;
use App\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @Author     : fang
 * @Date       : 2020-03-19 11:54
 * @Description:
 */
class RolePermission {

    /**
     * @Author     : fang
     * @Description:角色权限
     * @param $roleId
     * @return \Illuminate\Support\Collection
     */
    public static function permissionByRoleId($roleId) {
        if ($roleId === 1) {
            return Permission::get();
        }
        return RolePermissionModel::where('roleId', $roleId)->permissions()->get();
    }

    /**
     * @Author     : fang
     * @Description:用户权限
     * @param $userId
     * @return \Illuminate\Support\Collection
     */
    public static function permissionByUserId($userId) {
        if ($userId === 1) {
            return Permission::get();
        }
        $rolePermission = User::find($userId)->rolePermissions()->pluck('permissionId');
        return Permission::find($rolePermission);
    }

    /**
     * @Author     : fang
     * @Description:用户权限-根据权限编码
     * @param $userId
     * @param $accessCode
     * @return \Illuminate\Support\Collection
     */
    public static function permissionByUserIdAndCode($userId, $accessCode) {
        if ($userId === 1) {
            return Permission::get();
        }
        $permission = Permission::where('code', $accessCode)->whereIn('id',
            User::find($userId)->rolePermissions()->pluck('permissionId'))->first();
        return isset($permission->id) ? $permission->id : '';
    }

    /**
     * @Author     : fang
     * @Description:角色权限树
     * @param $roleId
     * @return array
     */
    public static function permissionByRoleIdTree($roleId) {

        $rolePermissionIds = Permission::whereHas('rolePermission', function (Builder $query) use ($roleId) {
            $query->where('roleId', $roleId);
        })->pluck('id')->toArray();
        if (Helper::userId() !== 1) {
            $selfRoleId = User::find(Helper::userId())->roleId;
            $permissionIds = RolePermissionModel::where('roleId', $selfRoleId)->pluck('permissionId')->toArray();
            $permission = Permission::whereIn('id', $permissionIds)->orderBy('sort')->get(['id',
                'name',
                'code',
                'parentId',
                'icon',
                'url',
                'component',
                'isNav']);
        } else {
            $permission = Permission::get(['id',
                'name',
                'code',
                'parentId',
                'icon',
                'url',
                'component',
                'isNav']);
        }
        $permission = Utils::std2arr($permission);
        foreach ($permission as $k => $v) {
            if (in_array($v['id'], $rolePermissionIds)) {
                $permission[$k]['checked'] = 1;
            } else {
                $permission[$k]['checked'] = 0;
            }
        }
        $tree = Utils::arr2tree($permission);
        return $tree;
    }

    public static function isPermissionRouteMap($permissionId, $route) {
        //TODO 这里要处理
        return true;
        //        return PermissionRoute::where('permissionId', $permissionId)->whereHas('route', function (Builder $query) use ($route) {
        //            $query->where('url', $route);
        //        })->count();
    }
}
