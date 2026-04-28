<?php

namespace App\Providers;

use App\Infrastructure\Security\Acl\AclRegistrar;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(AclRegistrar $aclRegistrar): void
    {
        $aclRegistrar->register();
    }
}
