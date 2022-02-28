<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class Modal extends Component
{
    public Product $producto;

    protected $listeners = ['mostrarModal'];

    public function mostrarModal($id)
    {
        $this->producto = Product::with(['images','category'])->find($id);
        $this->emit('mostrarModalBlade', $this->producto);
        
    }

    public function render()
    {
        return view('livewire.modal');
    }
}
