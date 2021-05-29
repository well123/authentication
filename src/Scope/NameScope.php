<?php


namespace Wubin\Authentication\Scope;


trait NameScope {

    public function scopeName($query, $name = null) {
        if (empty($name)) {
            return $query;
        } else {
            return $query->where('name', 'like', "%{$name}%");
        }
    }
}
