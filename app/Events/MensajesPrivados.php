<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajesPrivados implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userIdReceptor;
    public $userIdEmisor;
    public $mensajePrivado;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userIdReceptor,$userIdEmisor,$mensajePrivado)
    {
        $this->userIdReceptor = $userIdReceptor;
        $this->userIdEmisor = $userIdEmisor;
        $this->mensajePrivado = $mensajePrivado;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('mensajes-privados.'.$this->userIdReceptor);
    }
}
