<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\CatatLoginLogout;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            CatatLoginLogout::class,
        ],
        Logout::class => [
            CatatLoginLogout::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
