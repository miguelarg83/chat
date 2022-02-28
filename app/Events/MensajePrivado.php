<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MensajePrivado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userIdReceptor;
    public $userIdEmisor;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userIdReceptor,$userIdEmisor)
    {
        $this->userIdReceptor = $userIdReceptor;
        $this->userIdEmisor = $userIdEmisor;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */

    // Este evento es recibido  por todas las personas del chat.
    public function broadcastOn()
    {
        // Al ser una ruta privada hay que definirla en routes/channels.php. Es privada, solo para el usuario que ha recibido el click en su nombre.
        return new PrivateChannel('mensaje-privado.'.$this->userIdReceptor);
    }

    // Esto es para mandarle una variable a las rutas privadas de channels.php
    // public function broadcastWith()
    // {
    //     return ['user_id'=>$this->user_id];
    // }
}
