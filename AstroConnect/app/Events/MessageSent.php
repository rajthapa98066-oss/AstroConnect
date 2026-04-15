<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $roomId,
        public readonly string $message,
        public readonly string $senderName,
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("chat.{$this->roomId}");
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'senderName' => $this->senderName,
        ];
    }
}
