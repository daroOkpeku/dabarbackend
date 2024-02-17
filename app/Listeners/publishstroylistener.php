<?php

namespace App\Listeners;

use App\Events\publishstroyevent;
use App\Mail\Editorsemail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class publishstroylistener
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
    public function handle(publishstroyevent $event): void
    {
        $data = [
            'id'=>$event->id,
            'heading'=>$event->heading
        ];
        $users = User::where(['role'=>'Editor'])->get();

        $users->each(function ($user) use ($data) {
            $fullname = $user->lastname." ".$user->firstname;
            Mail::to($user->email)->send(new Editorsemail($data, $fullname));
        });

        //Mail::to()->send( new Editorsemail($data));
    }
}
