<?php
namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class EnumServiceProvider extends ServiceProvider {
    public function boot() {
        // see https://github.com/laravel/framework/issues/1346
        $platform = DB::getDoctrineSchemaManager()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
    }
}