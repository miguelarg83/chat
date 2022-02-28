<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajePublico implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    //private $mensaje;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->mensaje=$mensaje;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() // Este evento es recibido  por todas las personas del chat cuando alguien escribe algo.
    {
        return new PrivateChannel('msn'); // Al ser una ruta privada hay que definirla en routes/channels.php. Es privada, solo para los usuarios registrados.
    }

    // public function broadcastWith()
    // {
    //     return ['mensajeId'=>$this->mensaje->id];
    // }

    // public function broadcastAs()
    // {

    // }
}
