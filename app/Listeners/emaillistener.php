<?php

namespace App\Listeners;

use App\Events\emailevent;
use App\Mail\Sendemail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class emaillistener
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
    public function handle(emailevent $event): void
    {
        $data = [
            'name'=>$event->firstname." ".$event->lastname,
            'email'=>$event->email,
            "code"=>$event->verification_code
        ];
         Mail::to($event->email)->send( new Sendemail($data));

    }
}
