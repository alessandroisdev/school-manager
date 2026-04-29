<?php

namespace App\Listeners;

use App\Events\StudentEnrolledEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendStudentEnrolledNotification
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
    public function handle(StudentEnrolledEvent $event): void
    {
        if ($event->student->email) {
            \Illuminate\Support\Facades\Mail::to($event->student->email)
                ->send(new \App\Mail\StudentEnrolledMail($event->student, $event->registration, $event->password));
        }

        // Send to Guardian as well
        $guardian = $event->student->guardians()->first();
        if ($guardian && $guardian->email) {
            \Illuminate\Support\Facades\Mail::to($guardian->email)
                ->send(new \App\Mail\StudentEnrolledMail($event->student, $event->registration, $event->password));
        }
    }
}
