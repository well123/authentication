<?php

namespace Wubin\Authentication\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\PermissionRoute
 *
 * @property int $id
 * @property int $permissionID 权限ID
 * @property int $routeId 路由ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Wubin\Authentication\Models\Route|null $route
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute query()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute wherePermissionID($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionRoute whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PermissionRoute extends Model {

    public function route() {
        return $this->hasOne('App\Http\Models\Route', 'id', 'routeId');
    }
}
