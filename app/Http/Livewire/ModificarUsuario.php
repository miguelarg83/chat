<?php

namespace App\Http\Livewire;

use App\Models\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Imagin;

class ModificarUsuario extends Component
{
    use WithFileUploads;

    // Navegador: aplication -> cookies -> click enlace para ver token enviado con formulario
    public User $user;
    public $user_id;
    public $password;
    public $password_old;
    public $password_confirmation;
    public $photos=[];
    public $photosBD;

    //protected $listeners=['modificarUsuario'=>'$refresh']; // Para refrescar el componente, en lugar de poner esto en la vista se puede poner wire:click="$refresh"

    // protected $rules= [
    //     // 'end_page' => 'required_with:initial_page|integer|min:'. ($init_page+1) .'|digits_between:1,5'
    //     'user.name'      => 'required|string|min:4|max:255',
    //     'user.email'     => "required|string|max:255|unique:users,email,{$this->user->id}",
    //     'password'  => 'required|string|regex:/(?=^.{8,}$)(?=.*?[A-Z])(?=.*?[0-9])/', // https://riptutorial.com/es/regex/example/18996/una-contrasena-que-contiene-al-menos-1-mayuscula--1-minuscula--1-digito--1-caracter-especial-y-tiene-una-longitud-de-al-menos-10
    //     'password_confirmation' =>'same:password',
    // ];

    protected $messages=[
        'user.name.string'  =>  'Esto no es un string', // Personaliza mensajes de error
        //'user.email.unique' =>  'Este email ya existe en nuestra base de datos',
        'password.regex'    =>  'La contraseña debe tener al menos 8 caracteres, una mayuscula y un número',
        'password_confirmation.same'    =>  'Las contraseñas deben ser iguales',
        'photos.*.max'      =>  'Ninguna imagen debe ser de más de 2 MB',
        'photos.*.mimes'    =>  'Todos los archivos deben ser una imagen',
        'photos.max'        =>  'No se permiten mas de tres archivos'
    ];

    protected $validationAttributes = [
        'user.name' => 'Nombre',
        //'user.email' => 'Email',
        'password_old' => 'Vieja Contraseña',
        'password' => 'Contraseña',
        'password_confirmation' => 'Nueva Contraseña',
    ];

    public function mount()
    {
        $this->user=auth()->user();
        $this->user_id=auth()->user()->id;
        //$this->photosBD=Image::where('user_id',$this->user->id)->get(); // Esto me consigue las imagenes del usuario.
        $this->photosBD=$this->user->images()->orderBy('orden')->get();
    }

    public function rules()
    {
        return[
            'user.name' => ['required','string','min:3','max:15',Rule::unique('users','name')->ignore($this->user_id)],
            //'user.email'=> ['required','string','max:255',Rule::unique('users','email')->ignore($this->user_id)],
            'password'  => 'required|string|regex:/(?=^.{8,}$)(?=.*?[A-Z])(?=.*?[0-9])/',
            'password_confirmation' =>'same:password',
        ];
    }

    public function eliminarPhoto(Image $photo)
    {
        // if (Gate::allows('eliminar-actualizar-logo', $photo)) {
        //     abort(403);
        // }
        Gate::authorize('eliminar-actualizar-logo', $photo); // Solo podrá eliminar la imagen si le pertenece.
        $photo->delete();
        Storage::disk('public')->delete('photos/'.$photo->nombre);
        $this->photosBD=$this->user->images()->orderBy('orden')->get();
        $this->emitTo('logo','actualizarLogo');
    }

    public function eliminarTemporales()
    {
        $this->photos=[];
    }

    public function updatedPhotos()
    {   
        //dd($this->photos[0]->getClientOriginalName());
        $this->validate([
            'photos.*' => 'mimes:png,jpg|max:2048', // 2MB Max
            'photos' => 'max:3'
        ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function ordenar($ordenarIds)
    {
        //dd($ordenarIds);
        foreach($ordenarIds as $ordenarId) {
            $photo=Image::find($ordenarId['value']);
            Gate::authorize('eliminar-actualizar-logo', $photo); // Solo podrá cambiar la imagen si le pertenece.
            $photo->update([
                'orden' => $ordenarId['order'],
            ]);

        }
        $this->photosBD=$this->user->images()->orderBy('orden')->get();
        $this->emitTo('logo','actualizarLogo');
        //dd($this->photosBD);
    }

    public function submit()
    {
        sleep(1);
        if(Hash::check($this->password_old,auth()->user()->password))
        {
            $this->validate([
                'photos.*' => 'mimes:png,jpg|max:2048', // 1MB Max
                'photos' => 'max:3'
            ]);
            $cphotosBD=$this->photosBD->count();
            $cphotosInput=count($this->photos);
            $totalPhotos=$cphotosBD + $cphotosInput;
            if($totalPhotos<=3){
                foreach ($this->photos as $key=>$photo) {
                    // Comprobamos si el nombre de la imagen existe en la base de dados, 
                    // si es así vamos añadiendo un numero al final hasta que el nombre sea único.
                    // --------------------------------------------------------------------------- //
                    $nombreOriginalPhoto=$photo->getClientOriginalName();
                    $coincidePhoto=Image::where('nombre',$nombreOriginalPhoto)->first();
                    if($coincidePhoto)
                    {
                        $extension=explode(".",$nombreOriginalPhoto);
                        $i=1;
                        while($coincidePhoto)
                        {
                            $nombreTemporalPhoto=$extension[0].$i.'.'.$extension[1];
                            $coincidePhoto=Image::where('nombre',$nombreTemporalPhoto)->first();
                            $i++;
                        }
                        $nombreOriginalPhoto=$nombreTemporalPhoto;
                    }
                    // --------------------------------------------------------------------------- //
                    Image::create([
                        'nombre'    =>  $nombreOriginalPhoto, // $photo->store('')
                        'orden'     =>  $key+1,
                        'user_id'   =>  $this->user->id,
                    ]);
                    $this->photosBD=$this->user->images()->orderBy('orden')->get();
                    // ->storeAs('your_path', $nombre  ,'your_disk');
                    //Imagin::configure();
                    $photo->storeAs('public/photos',$nombreOriginalPhoto);
                    // Se puede guardar la foto primero sin retocar en otra carpeta con la instrucción de arriba   
                    //$photo= Imagin::make($photo)->resize(1920, 1080); //150*100
                    //$photo->save('storage/photos/'.$nombreOriginalPhoto);
                }
            }
            else $this->addError('photos', 'Ya tiene '.$cphotosBD.' imagenes almacenadas, solo puede tener un máximo de 3');
            $this->user->save();
            $this->reset(['photos','password','password_old','password_confirmation']);
            $this->emitTo('logo','actualizarLogo');
            session()->flash('message','Datos guardado correctamente');
            
        }

        else    $this->addError('password_old', 'La contraseña no es correcta.');
    }

    public function render()
    {
        return view('livewire.modificar-usuario');
    }
}
