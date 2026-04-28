<?php

namespace App\Infrastructure\Security\Acl;

use App\Domains\Auth\Models\Permission;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Schema;

class AclRegistrar
{
    public function __construct(
        protected Gate $gate,
        protected Repository $cache
    ) {}

    public function register(): void
    {
        $this->gate->before(function ($user, $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
            return null;
        });

        if (!Schema::hasTable('permissions')) {
            return;
        }

        $permissions = Permission::with('roles')->get();

        foreach ($permissions as $permission) {
            $this->gate->define($permission->name, function ($user) use ($permission) {
                return $user->hasPermission($permission->name);
            });
        }
    }
}
