<?php

namespace App\Listeners;

use App\Events\roleevent;
use App\Mail\roleemail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class rolelistener
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
    public function handle(roleevent $event): void
    {
        $data = [
            "firstname"=>$event->firstname,
            "lastname"=>$event->lastname,
            "email"=>$event->email,
            "verification_code"=>$event->verification_code,
            "role"=>$event->role
        ];

       Mail::to($event->email)->send( new roleemail($data) );
    }
}
