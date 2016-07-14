<?php

namespace App\Providers;

use App\Traits\ResponseHandlerTrait;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    use ResponseHandlerTrait;

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

            return $this->setStatusCode(400)->respondWithError(["Please supply user token."], "Token Absent");

        });

        $events->listen('tymon.jwt.invalid', function () {

            return $this->setStatusCode(400)->respondWithError(["The supplied token is invalid. Please supply a correct Token."], "Invalid Token");

        });
    }
}
