<?php

namespace App\Mail;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentEnrolledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $registration;
    public $password;

    public function __construct(Student $student, string $registration, string $password)
    {
        $this->student = $student;
        $this->registration = $registration;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao SGE! Sua matrícula foi confirmada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-enrolled',
        );
    }
}
