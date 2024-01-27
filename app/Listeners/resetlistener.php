<?php

namespace App\Listeners;

use App\Events\resetevent;
use App\Mail\SendResetemail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class resetlistener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(resetevent $event): void
    {
        $data = [
            "email"=>$event->email,
        ];

        Mail::to($event->email)->send(new SendResetemail($data));
    }
}
