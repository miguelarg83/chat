@extends('layouts.app')

@section('content')

<div class="container">
    {{-- @livewire('user-table') --}}
    <livewire:products-table/>
    <livewire:modal/>
     {{-- <livewire:datatable model="App\Models\Product"
     include="id, nombre, created_at"
     dates="created_at|m-d-Y h:i:s"
     exportable
    /> --}}
</div>

@endsection

