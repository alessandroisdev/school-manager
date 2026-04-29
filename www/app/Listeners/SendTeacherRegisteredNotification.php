<?php

namespace App\Listeners;

use App\Events\TeacherRegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTeacherRegisteredNotification
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
    public function handle(TeacherRegisteredEvent $event): void
    {
        if ($event->email) {
            \Illuminate\Support\Facades\Mail::to($event->email)
                ->send(new \App\Mail\TeacherRegisteredMail($event->teacher, $event->email, $event->password));
        }
    }
}
