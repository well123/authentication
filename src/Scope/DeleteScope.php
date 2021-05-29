<?php


namespace Wubin\Authentication\Scope;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DeleteScope implements Scope {

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model) {
        $builder->where('isDelete', 0);
    }
}
