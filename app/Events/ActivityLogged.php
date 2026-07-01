<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;

    /**
      * Create a new event instance.
      */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
      * Get the channels the event should broadcast on.
      */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('activities'),
        ];
    }

    /**
      * The event's broadcast name.
      */
    public function broadcastAs(): string
    {
        return 'activity.logged';
    }

    /**
      * Get the data to broadcast.
      */
    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
