<?php

namespace App\Http\Livewire;

use Livewire\Component;

class RegistroChulo extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirm;
    protected $rules= [
        // 'end_page' => 'required_with:initial_page|integer|min:'. ($init_page+1) .'|digits_between:1,5'
        'name'      => 'required|string|min:4|max:15|unique:users',
        'email'     => 'required|string|email|max:50|unique:users',
        'password'  => 'required|string|regex:/(?=^.{8,}$)(?=.*?[A-Z])(?=.*?[0-9])/', // https://riptutorial.com/es/regex/example/18996/una-contrasena-que-contiene-al-menos-1-mayuscula--1-minuscula--1-digito--1-caracter-especial-y-tiene-una-longitud-de-al-menos-10
        'password_confirm' =>'same:password',
    ];
    protected $messages=[
        'name.string'       => 'Esto no es un string', // Personaliza mensajes de error
        'password.regex'    => 'La contraseña debe tener al menos 8 caracteres, una mayuscula y un número'
    ];

    protected $validationAttributes = [
        'name' => 'Nombre',
        'password' => 'Contraseña'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.registro-chulo');
    }
}
