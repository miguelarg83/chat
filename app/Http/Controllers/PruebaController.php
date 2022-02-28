<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PruebaController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function store(Request $request)
    {
        $messages = [
            'inicio.in' => 'Un articulo de subscripción no puede estar en el inicio'
        ];

        $validator = Validator::make([
            '_token'=>$request->_token,'inicio'=>$request->inicio,'suscripcion'=>$request->suscripcion],
            [],
            $messages
        );

        $validator->sometimes('inicio', 'in:no', function ($request) {
            return $request->suscripcion==="si";
        });

        
        if($validator->fails())
        {
           return $validator->validate();
        }

        return back();
    }
}
