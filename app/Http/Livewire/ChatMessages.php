<?php

namespace App\Http\Livewire;

use App\Events\MensajePrivado;
use App\Events\MensajesPrivados;
use App\Events\MensajePublico;
use App\Events\Typing;
use App\Events\NoTyping;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ChatMessages extends Component
{
    public $user; // Objeto $user que recibirá todos los atributos del usuario autenticado en el mount() : $this->user=auth()->user();
    public $mensajes;
    public $mensaje;
    public $mensajePrivado;
    public $usuarios=[];
    public $usuarioReceptor; // Objeto que recibirá al usuario junto con sus imágenes cuando abrimos un privado. 

    //protected $listeners=['echo:msn,MensajePublico'=>'mensajeTodos'];

    public function mount()
    {
        session(['receptor'=>'general']);
        $this->user=auth()->user();
        $this->mensajes=Message::with('user.images')->latest()->take(50)->get()->reverse(); 
        // Coge los 50 registros más nuevos y los mostramos a la inversa de como los obtenemos de la BD.
        // El metodo mount se carga solo una vez al inicio y cada vez que cargamos la página por eso traemos las imagenes ,si existen, de cada usuario.
        // Lo podemos convertir en array con ->values()->toArray().(oldest es lo contrario).
        // $this->mensajes=$mensajes->sortBy('id'); // ya no hace falta con reverse.
    }

    public function getListeners()
    {
        return [
            "echo-private:mensaje-privado.{$this->user->id},MensajePrivado" => 'mensajePrivado',
            // Cuando alguien hace click en el nombre de alguien se ejecuta esto, $this->user->id es el id del usuario receptor,el que recibe el click.
            // Se ejecuta el método mensajesPrivados que recibe el id del usuario receptor, el que recibe el click.
            "echo-private:mensajes-privados.{$this->user->id},MensajesPrivados" => 'mensajesPrivados',
            /* Todos los usuarios refrescarán los datos de su vista cuando esta ruta sea llamada, esto es cuando alguien del chat escriba algo.
               Esto hará que vean lo que alguien escribió sin recargar la página manualmente */
            'echo-private:msn,MensajePublico' => '$refresh',
            // El evento se dispara cada vez que alguien está escribiendo en un chat privado
            "echo-private:typing.{$this->user->id},Typing" => 'typing',
            // El evento se dispara cada vez que alguien que estaba escribiendo en un chat privado deja de hacerlo
            "echo-private:no-typing.{$this->user->id},NoTyping" => 'noTyping',
            // El evento here se dispara cada vez que alguien entra en el chat, este evento llama al método here.
            'echo-presence:presencia,here' => 'here',
            'echo-presence:presencia,joining' => 'joining',
            'echo-presence:presencia,leaving' => 'leaving',
        ];
    }

    // public function mensajeTodos($mensajeId)
    // {
    //     $mensaje=Message::with('user')->where('id',$mensajeId)->first(); // No vale ni find ni get porque devuelven una colección de un objeto y first devuelve objeto pelado
    //     $this->mensajes->push($mensaje);
    // }

    public function here($users)
    {
        // Este método se ejecuta cada vez que entra alguien en el chat.
        // Debido a este método $this->usuarios es igualado a un array en lugar de a una colección.
        // Por eso en la vista menejamos arrays de usuarios en lugar de objetos.
        $this->usuarios=$users;
        $this->usuarios=collect($users)->sortBy('name');
        $this->usuarios=$this->usuarios->toArray();
        foreach($this->usuarios as $u)
        {
            $data[]=$u;
        }
        $this->usuarios=$data;
    }

    public function joining($user)
    {
        $this->usuarios[]=$user;
        $this->usuarios=collect($this->usuarios)->sortBy('name');
        $this->usuarios=$this->usuarios->toArray();
        foreach($this->usuarios as $u)
        {
            $data[]=$u;
        }
        $this->usuarios=$data;
        // if(!in_array($user,$this->usuarios)) 
        //     array_push($this->usuarios,$user);
    }

    public function leaving($user)
    {
        foreach($this->usuarios as $key => $u)
        {
            if($u['id']==$user['id'])
                unset($this->usuarios[$key]);
        }

        // $here = collect($this->usuarios);

        // $firstIndex = $here->search(function ($authData) use ($user) {
        //     return $authData['id'] == $user['id'];
        // });

        // $here->splice($firstIndex, 1);

        // $this->usuarios = $here->toArray();
    }

    public function chatGeneral()
    {
        session(['receptor'=>'general']);
    }

    // Cuando recibo un mensaje privado de alguien.
    public function mensajesPrivados($array) 
    {
        $usuarioEmisor=User::whereId($array["userIdEmisor"])->first();

        if(session('receptor')!=$usuarioEmisor->name) // Si me encuentro en la caja de privado de la persona que me está enviando los mensajes no se mete dentro.
        {
            if(session($usuarioEmisor->name.'C')==null) // Si la sesión no existe la creo y la igualo a 1.
                session([$usuarioEmisor->name.'C'=>0]);
            session()->increment($usuarioEmisor->name.'C');
            // Creo una sesión única con el nombre de la persona que me manda los mensajes y la igualo a su nombre
            session([$usuarioEmisor->name.'N'=>$usuarioEmisor->name]);

            session()->push('naranjas',$usuarioEmisor->name);
        }

        //$mensajePrivado=$usuarioEmisor->name.": ".$array["mensajePrivado"];
        session()->push($usuarioEmisor->name,$array["mensajePrivado"]);
    }
    
    // Cuando mandamos un mensaje al chat público ejecutamos este método
    public function submit() 
    {
        // Validamos que el input contiene al menos un string de dos caracteres.
        $this->validate([
            'mensaje' => 'required|max:500|min:2',
        ]);

        // Para evitar que se puedan mandar mensajes con palabras de más de 40 caráteres para evitar desbordamiento
        $words = explode(' ', $this->mensaje);
        $long_words = [];
        foreach ($words as $word) {
            if (strlen($word) > 40) {
                $long_words[] = $word;
            }
        }
        
        /* Guardamos el mensaje escrito en la BD junto con el id del usuario que lo ha escrito siempre y cuando
        el mensaje no tenga palabras de más de 40 caracteres */
        if(count($long_words)==0){
            Message::create([
                'mensaje' => $this->mensaje,
                'user_id' => $this->user->id,
            ]);
        
            // Vaciamos el input
            //$this->mensajes->push($mensaje);
            $this->reset(['mensaje']);
            /* Emitimos el evento scroll que será escuchado por la vista de este componente llamado chat-messages.php 
            a través de esto:"Livewire.on('scroll', () => {" para que el scroll vaya hacia abajo automaticamente al enviar un mensaje */
            $this->emit('scroll');
            /* Emitimos un evento para avisar a todo el mundo que alguien envió un mensaje al chat general
            El evento está en app/Events/MensajePublico */
            broadcast(new MensajePublico())->toOthers(); // Este evento se difundirá a todos menos a mi.
        }
    }

    // Cuando hago click en el nombre de alguien para abrirle un privado
    public function saludo($userIdReceptor) 
    {
        $coincide=false;
        //$usuarioEmisor=auth()->user();
        // User::find($userIdReceptor); ??
        $usuarioReceptor=User::whereId($userIdReceptor)->first(); 
        // Si existe la sesión saludos
        if(session('saludos')!=null)
        {
            foreach(session('saludos') as $saludo)
            {
                // Comprobamos si el usuario al que hemos clicado existe en el array de sesión saludos
                // Si existe eso significará que ya le hemos clicado varias veces.
                if($saludo==$usuarioReceptor->name)
                    $coincide=true;
            }

            // Si es la primera vez que le hemos clicado se mete aquí y mete su nombre dentro del array de sesión saludos.
            // Creamos una variable receptor donde también metemos su nombre
            // Metemos el id de la persona que ha clicado a la otra persona, esto es, el emisor del saludo
            // Emitimos un evento, el evento está en app/Events/MensajePrivado
            if(!$coincide)
            {
                session()->push('saludos',$usuarioReceptor->name);
                session(['receptor'=>$usuarioReceptor->name]);
                $userIdEmisor=auth()->user()->id;
                // Aquí seleccionamos el usuario al que hemos clickado sobre su nombre con sus imagenes.
                $this->usuarioReceptor=User::with('images')->whereId($userIdReceptor)->first();
                broadcast(new MensajePrivado($userIdReceptor,$userIdEmisor));
            }
        }
        else
        {
            session()->push('saludos',$usuarioReceptor->name);
            session(['receptor'=>$usuarioReceptor->name]);
            $userIdEmisor=auth()->user()->id;
            // Aquí seleccionamos el usuario al que hemos clickado sobre su nombre con sus imagenes.
            $this->usuarioReceptor=User::with('images')->whereId($userIdReceptor)->first();
            broadcast(new MensajePrivado($userIdReceptor,$userIdEmisor));
        }
    }

    // Cuando hacen click en mi nombre para abrirme un privado se me ejecuta este método, $array recibe los valores del evento MensajePrivado.php
    public function mensajePrivado($array) 
    {
        $usuarioEmisor=User::whereId($array["userIdEmisor"])->first();
        //$usuarioReceptor=User::whereId($array["userIdReceptor"])->first();
        $coincide=false;
        // La persona que me saludó ya metió en su array de sesión saludos mi nombre, ahora yo meto en mi array de sesión su nombre
        if(session('saludos')!=null)
        {
            // Recorro el array para ver si tengo su nombre
            foreach(session('saludos') as $saludo)
            {
                if($saludo==$usuarioEmisor->name)
                    $coincide=true;
            }
            if(!$coincide)
            // Si no lo tengo meto su nombre en el array de sesión saludos.
            {
                // Creo un array de sesión llamado naranjas para pintar de naranja la caja de las personas que me han dado click por primera vez
                // y yo aún no me metí en su privado, una vez me meta en su privado ya nunca volverá a aparecer de ese color.
                session()->push('naranjas',$usuarioEmisor->name);
                session()->push('saludos',$usuarioEmisor->name);
                // sesión flash??
                // se ejecuta un sonido en mi ordenador.
                $this->emit('sonidoPrivado');
            }
        }
        else
        {
            // Creo un array de sesión llamado naranjas para pintar de naranja la caja de las personas que me han dado click por primera vez
            // y yo aún no me metí en su privado, una vez me meta en su privado ya nunca volverá a aparecer de ese color.
            session()->push('naranjas',$usuarioEmisor->name);
            session()->push('saludos',$usuarioEmisor->name);
            // sesión flash??
            $this->emit('sonidoPrivado');
        }
    }

    // Cuando cierro la caja-nombre de privado de alguien
    public function eliminarSesion($saludo) 
    {
        // Lo elimino del array de sesión saludos
        $saludos = session()->get('saludos');
        foreach($saludos as $key => $s)
        {
            if($s==$saludo)
                unset($saludos[$key]);
        }
        session()->put('saludos', $saludos);
        if(empty(session('saludos'))) session()->forget('saludos');

        // Debo eliminar su nombre del array de sesión naranjas
        if(session('naranjas')!=null)
        {
            $naranjas = session()->get('naranjas');
            foreach($naranjas as $key => $s)
            {
                if($s==$saludo)
                    unset($naranjas[$key]);
            }
            session()->put('naranjas', $naranjas);
            if(empty(session('naranjas'))) session()->forget('naranjas');
        }
        
        // Debo eliminar su contador
        if(session($saludo.'C')!=null) session()->forget($saludo.'C');
        
        // Debo eliminar su sesión de color
        if(session($saludo.'N')!=null) session()->forget($saludo.'N');

        if(session($saludo)!=null) session()->forget($saludo);
        if(session('receptor')!=null && session('receptor')==$saludo) session()->forget('receptor');
    }

    /* Cuando hago click en una caja-nombre de privado de alguien se ejecuta esta función que recibe el nombre del receptor
       que es la otra persona y no yo */
    public function chatPrivado($userNombreReceptor) 
    {    
        /* Al dar click sobre la caja-nombre de una persona debo eliminar su nombre del array de sesión naranjas.
           Pero antes comprobamos que existe el array de sesión naranjas. */
        if(session('naranjas')!=null)
        {
            // Igualamos el array de sesión naranjas al array naranjas para que no de error.
            $naranjas = session()->get('naranjas');
            foreach($naranjas as $key => $s)
            {
                if($s==$userNombreReceptor)
                    unset($naranjas[$key]);
            }
            session()->put('naranjas', $naranjas);
            // Si el array de sesión naranjas está vacio lo eliminamos
            if(empty(session('naranjas'))) session()->forget('naranjas');
        }

        // Al dar click sobre la caja-nombre de la persona debo eliminar el conteo de mensajes no leidos si es que existe algún mensaje no leido.
        if(session($userNombreReceptor.'C')!=null) session()->forget($userNombreReceptor.'C');
        if(session($userNombreReceptor.'N')!=null) session()->forget($userNombreReceptor.'N');
        // Si se crea esta variable de sesión significa que hemos hecho click sobre una caja nombre y por lo tanto hemos abierto un chat privado.
        session(['receptor'=>$userNombreReceptor]);
        // Aquí seleccionamos el usuario al que hemos clickado sobre la caja nombre con sus imagenes.
        $this->usuarioReceptor=User::with('images')->where('name',$userNombreReceptor)->first();
    }

    /* Cuando estoy escribiendo en un chat privado , mientras escribo se ejecuta esta función.
    Quiero que la persona sepa que estoy escribiendo y por lo tanto ejecuto un evento.
    Este evento será privado porque solo quiero que le llegue a la persona a la cual escribo */
    
    public function updatingMensajePrivado()
    {
        $usuarioNombreReceptor=session('receptor');
        $usuarioIdEmisor=$this->user->id;
        $usuarioIdReceptor=User::where('name',$usuarioNombreReceptor)->value('id');
        broadcast(new Typing($usuarioIdEmisor,$usuarioIdReceptor))->toOthers();
    }

    /* Si Ana me escribe o yo le escribo a Ana se ejecutará este código, supongamos que Ana me escribe,
    una vez me esté escribiendo este método se ejecutará en mi ordenador, siendo yo el Receptor y Ana el emisor */
    public function typing($array)
    {
        $usuarioNombreEmisor=User::whereId($array["usuarioIdEmisor"])->value('name');
        $coincide=false;
        // Si existe el array de sesión typing se mete aquí
        if(session('typing')!=null)
        {
            // Recorro el array para ver si tengo su nombre
            foreach(session('typing') as $typing)
            {
                if($typing==$usuarioNombreEmisor)
                    $coincide=true;
            }
            if(!$coincide)
            // Si no lo tengo meto su nombre en el array de sesión typing.
            {
                // Creo un array de sesión llamado typing que contendrá las personas que me están escribiendo en el momento presente
                session()->push('typing',$usuarioNombreEmisor);
            }
        }
        // Si no existe el array de sesión typing se mete aquí y lo crea por primera vez.
        else{
            session()->push('typing',$usuarioNombreEmisor);
        }

        /*  En el front-end comprobamos si el nombre de cada caja de privado está contenido en la variable de sesión typing
        si esto es así esa o esas personas me están escribiendo algo en ese instante y sacaría lo de escribiendo en la caja nombre de quien me escriba */
    }

    // Cuando dejo de escribir en un privado hago que se me ejecute este método que creará un evento de difusión a la persona a la que dejo de escribir
    public function updatedMensajePrivado()
    {
        usleep( 500000 ); // Dormir durante medio segundo
        //sleep(1); // Para que lo de Miguel escribiendo no le desaparezca tan rápido a Flor.
        if(session('receptor')!=null)
            $usuarioNombreReceptor=session('receptor');
        $usuarioIdEmisor=$this->user->id;
        $usuarioIdReceptor=User::where('name',$usuarioNombreReceptor)->value('id');
        broadcast(new NoTyping($usuarioIdEmisor,$usuarioIdReceptor))->toOthers();
    }

    // Cuando dejo de escribir a Flor un privado Flor ejecutará este método
    public function noTyping($array)
    {
        $usuarioNombreEmisor=User::whereId($array["usuarioIdEmisor"])->value('name');
        $typings = session()->get('typing');
        foreach($typings as $key => $typing)
        {
            if($typing==$usuarioNombreEmisor)
                unset($typings[$key]);
        }
        session()->put('typing', $typings);
        if(empty(session('typing'))) session()->forget('typing');
    }

    // Cuando envío un mensaje a alguien por privado yo ejecuto este método
    public function submitPrivado() 
    {
        $this->validate([
            'mensajePrivado' => 'required|max:500|min:1',
        ]);

        $horaActual=Carbon::now();
        $horaActual=$horaActual->format('h:i A');
        $mensajePrivado=$this->user->name." (".$horaActual.")  :  ".$this->mensajePrivado;
        session()->push(session('receptor'),$mensajePrivado);
        $this->reset(['mensajePrivado']);
        $this->emit('scroll');
        $userIdEmisor=auth()->user()->id;
        $userIdReceptor=User::where('name',session('receptor'))->value('id');
        broadcast(new MensajesPrivados($userIdReceptor,$userIdEmisor,$mensajePrivado))->toOthers(); // Este evento se difundirá a todos menos a mi.
    }

    public function render()
    {
        $this->mensajes=Message::with('user.images')->latest()->take(50)->get()->reverse();
        return view('livewire.chat-messages');
    }
}
