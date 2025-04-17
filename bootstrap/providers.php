<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\Authentication\App\Providers\AuthenticationServiceProvider::class,
    Modules\FileAndFolder\App\Providers\FileServiceProvider::class,
    Modules\Shared\SharedServiceProvider::class,
];
