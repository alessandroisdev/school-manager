<?php

namespace App\Mail;

use App\Domains\HR\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeacherRegisteredMail extends Mailable
{
    use Queueable, SerializesModels;

    public $teacher;
    public $email;
    public $password;

    public function __construct(Teacher $teacher, string $email, string $password)
    {
        $this->teacher = $teacher;
        $this->email = $email;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Corpo Docente do SGE!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.teacher-registered',
        );
    }
}
