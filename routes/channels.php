<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Retorna true solo si el usuario está autenticado, o sea, es diferente de null.
Broadcast::channel('msn', function ($user) { // la ruta se llama msn.
    return $user != null;
});

Broadcast::channel('presencia', function ($user) {
    // return [
    //     'id' => $user['id'],
    //     'name' => $user['name'],
    // ];
    return User::with('images')->where('id',$user['id'])->first();
});

// Comprobamos que el id del usuario es igual que el id del usuario que ha sido clicado por alguien.
/* Por ejemplo, si Ana abre privado a Miguel y hay 50 usuarios en el chat
   esta comprobación se hará a los 50 usuarios y solo Miguel pasaría el if */
Broadcast::channel('mensaje-privado.{userIdReceptor}',function($user,$userIdReceptor){
    //Log::debug($userIdReceptor); En el archivo .log aparecería esta info, borrar el archivo log para verlo más claro.
    if($user!=null && $user->id==$userIdReceptor)
        return true;
});

Broadcast::channel('mensajes-privados.{userIdReceptor}',function($user,$userIdReceptor){
    //Log::debug($userIdReceptor);
    if($user!=null && $user->id==$userIdReceptor)
        return true;
});

/* Yo le estoy escribiendo un privado a Flor, Flor es la única persona que debe entrar a este canal  */
Broadcast::channel('typing.{usuarioIdReceptor}',function($user,$usuarioIdReceptor){
    if($user!=null && $user->id==$usuarioIdReceptor)
        return true;
});

/* Yo le estaba escribiendo un privado a Flor, Flor es la única persona que debe entrar a este canal  */
Broadcast::channel('no-typing.{usuarioIdReceptor}',function($user,$usuarioIdReceptor){
    if($user!=null && $user->id==$usuarioIdReceptor)
        return true;
});



