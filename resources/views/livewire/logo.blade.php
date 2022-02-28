<div>
    @if($this->image)
        <img width="40px" src="{{ url('storage/photos/'.$this->image->nombre) }}" alt="hola">
    @endif
</div>
