<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeacherRegisteredEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teacher;
    public $email;
    public $password;

    /**
     * Create a new event instance.
     */
    public function __construct(\App\Domains\HR\Models\Teacher $teacher, string $email, string $password)
    {
        $this->teacher = $teacher;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
