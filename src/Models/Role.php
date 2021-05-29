<?php

namespace Wubin\Authentication\Models;

use Wubin\Authentication\Scope\DeleteScope;
use Wubin\Authentication\Scope\ExceptScope;
use Wubin\Authentication\Scope\NameScope;
use Wubin\Authentication\Scope\PaginateScope;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Role
 *
 * @property int $id
 * @property string $name 名称
 * @property int $isDelete
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Role except($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Role exceptAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder|Role name($name = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereIsDelete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role wuPaginate($page, $pageSize = 10)
 * @mixin \Eloquent
 */
class Role extends Model {

    use PaginateScope;
    use ExceptScope;
    use NameScope;

    protected static function booted() {
        //        static::addGlobalScope('id', function (Builder $builder) {
        //            $builder->where('id', '<>', 1);
        //        });
        static::addGlobalScope(new DeleteScope);
    }

    public function scopeExceptAdmin($query) {
        return $query->where('id', '<>', 1);
    }
}
