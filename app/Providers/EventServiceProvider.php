<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        $events->listen('tymon.jwt.absent', function () {

            return response()->json([
                'code' => 400,
                'error' => "Please supply user token."
            ], 400);

        });

        $events->listen('tymon.jwt.invalid', function () {

            return response()->json([
                'code' => 400,
                'error' => "The supplied token is invalid. Please supply a correct Token."
            ], 400);

        });
    }
}
