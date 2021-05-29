<?php


namespace Wubin\Authentication\Scope;


trait PaginateScope {

    public function scopeWuPaginate($query, $page, $pageSize = 10) {
        return $query->limit($pageSize)->offset(($page - 1) * $pageSize);
    }
}
