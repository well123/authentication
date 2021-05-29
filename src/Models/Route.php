<?php

namespace Wubin\Authentication\Models;

use Wubin\Authentication\Scope\ExceptScope;
use Wubin\Authentication\Scope\NameScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Http\Models\Route
 *
 * @property int $id
 * @property string $name 路由名称
 * @property string $url 路由地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Route except($id)
 * @method static Builder|Route name($name = null)
 * @method static Builder|Route newModelQuery()
 * @method static Builder|Route newQuery()
 * @method static Builder|Route query()
 * @method static Builder|Route url($url = null)
 * @method static Builder|Route whereCreatedAt($value)
 * @method static Builder|Route whereId($value)
 * @method static Builder|Route whereName($value)
 * @method static Builder|Route whereUpdatedAt($value)
 * @method static Builder|Route whereUrl($value)
 * @mixin \Eloquent
 */
class Route extends Model {

    use ExceptScope;
    use NameScope;

    protected static function booted() {
        static::addGlobalScope('id', function (Builder $builder) {
            $builder->select(['id', 'name', 'url'])->orderBy('id', 'desc');
        });
    }

    public function scopeUrl($query, $url = null) {
        if (empty($url)) {
            return $query;
        } else {
            return $query->where('url', 'like', "%{$url}%");
        }
    }
}
