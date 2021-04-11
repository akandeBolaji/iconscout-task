<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\{
    NewIconEvent,
    UpdateIconEvent,
    DeleteIconEvent
};
use App\Listeners\{
    NewIconListener,
    UpdateIconListener,
    DeleteIconListener
};

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        NewIconEvent::class => [
            NewIconListener::class,
        ],

        UpdateIconEvent::class => [
            UpdateIconListener::class,
        ],

        DeleteIconEvent::class => [
            DeleteIconListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
