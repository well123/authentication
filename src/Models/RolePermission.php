<?php

namespace Wubin\Authentication\Models;

use Wubin\Authentication\Scope\DeleteScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\RolePermission
 *
 * @property int $id
 * @property int $roleId 角色ID
 * @property int $permissionId 权限ID
 * @property int $isDelete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Wubin\Authentication\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission query()
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RolePermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RolePermission extends Model {

    protected static function booted() {
        static::addGlobalScope(new DeleteScope);
    }

    public function permissions() {
        return $this->hasMany('App\Http\Models\Permission', 'id', 'permissionId');
    }

    public function user() {
        return $this->belongsTo('App\User', 'roleId', 'roleId');
    }
}
