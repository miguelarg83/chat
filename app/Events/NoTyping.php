<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NoTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $usuarioIdReceptor;
    public $usuarioIdEmisor;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($usuarioIdEmisor,$usuarioIdReceptor)
    {
        $this->usuarioIdReceptor = $usuarioIdReceptor;
        $this->usuarioIdEmisor = $usuarioIdEmisor;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('no-typing.'.$this->usuarioIdReceptor);
    }
}