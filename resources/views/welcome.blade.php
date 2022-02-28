@extends('layouts.app')

@section('content')

{{-- <div class="container">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('store') }}">
        @csrf
        <div class="row justify-content-center">
            <div class="col-2">
                <legend>Inicio</legend>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="inicio" value="si" id="si">
                    <label class="form-check-label" for="si">
                        Si
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="inicio" value="no" id="no" checked>
                    <label class="form-check-label" for="no">
                        No
                    </label>
                </div>
            </div>

            <div class="col-2 offset-2">
                // crea un margen a la izquierda de la columna como si hubiera un div de tres columnas
                <legend>Subscripción</legend>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="suscripcion" value="si" id="si">
                    <label class="form-check-label" for="si">
                        Si
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="suscripcion" value="no" id="no" checked>
                    <label class="form-check-label" for="no">
                        No
                    </label>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-6 mt-3">
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
        </div>
    </form>
</div> --}}

<div style="height:735px;" class="container-lg">
    <livewire:chat-messages/>
</div>

@endsection
