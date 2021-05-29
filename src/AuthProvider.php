<?php


namespace Wubin\Authentication;


use Illuminate\Support\ServiceProvider;

class AuthProvider extends ServiceProvider {


    public function boot() {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
