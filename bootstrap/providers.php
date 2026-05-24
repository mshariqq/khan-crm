<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\PermissionServiceProvider::class,
    Laravel\Dusk\DuskServiceProvider::class,
];
