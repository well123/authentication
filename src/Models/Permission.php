<?php

namespace Wubin\Authentication\Models;

use Wubin\Authentication\Scope\DeleteScope;
use Wubin\Authentication\Scope\ExceptScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Permission
 *
 * @property int $id
 * @property string $name 权限名称
 * @property string $code 编码
 * @property string|null $url 路由
 * @property string|null $icon 图标
 * @property int $isNav 是否展示在导航栏
 * @property int $parentId 父级ID
 * @property string|null $component 前台组件
 * @property int $noCache 是否前台缓存页面
 * @property int $sort 排序
 * @property int $isDelete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Wubin\Authentication\Models\RolePermission $rolePermission
 * @method static \Illuminate\Database\Eloquent\Builder|Permission code($code = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission except($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereComponent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereIsNav($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereNoCache($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUrl($value)
 * @mixin \Eloquent
 */
class Permission extends Model {

    use ExceptScope;

    protected $fillable = ['name',
        'code',
        'url',
        'icon',
        'isNav',
        'parentId',
        'component',
        'noCache',
        'sort'];

    protected static function booted() {
        static::addGlobalScope(new DeleteScope);
    }

    public function scopeCode($query, $code = null) {
        if (empty($code)) {
            return $query;
        } else {
            return $query->where('code', $code);
        }
    }

    public function rolePermission() {
        return $this->belongsTo('App\Http\Models\RolePermission', 'id', 'permissionId');
    }

}
