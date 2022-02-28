<?php

namespace App\Http\Livewire;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Logo extends Component
{
    public User $user;

    protected $listeners=['actualizarLogo'=>'$refresh'];

    public function mount()
    {
        if(auth()->user())
            $this->user=auth()->user();
    }

    public function getImageProperty()
    {
        $valor=$this->user->images()->min('orden');
        return $this->user->images()->where('orden',$valor)->first();
    }

    public function render()
    {
        return view('livewire.logo');
    }
}
