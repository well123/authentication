<?php


namespace Wubin\Authentication\Scope;

trait ExceptScope {

    public function scopeExcept($query, $id) {
        return $query->where('id', '<>', $id);
    }
}
