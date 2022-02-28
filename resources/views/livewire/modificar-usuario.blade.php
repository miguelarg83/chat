<div>
    <form wire:submit.prevent="submit">
        <div>
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
        </div>
        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

            <div class="col-md-6">
                <input id="name" wire:model.debounce.500ms="user.name" type="text" class="form-control @error('user.name') is-invalid @enderror" name="name" required>

                @error('user.name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <!--
        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{-- {{ __('E-Mail Address') }} --}}</label>

            <div class="col-md-6">
                <input id="email" wire:model.debounce.500ms="user.email" type="text" class="form-control {{-- @error('user.email') is-invalid @enderror --}}" name="email">

                {{-- @error('user.email') --}}
                    <span class="invalid-feedback" role="alert">
                        <strong>{{-- {{ $message }} --}}</strong>
                    </span>
                {{-- @enderror --}}
            </div>
        </div>
        -->
        <div class="form-group row" x-data="{ isUploading: false, progress: 0 }"
        x-on:livewire-upload-start="isUploading = true"
        x-on:livewire-upload-finish="isUploading = false"
        x-on:livewire-upload-error="isUploading = false"
        x-on:livewire-upload-progress="progress = $event.detail.progress">
            <label class="col-md-4 col-form-label text-md-right">Máximo 3 imágenes</label>

            <div class="col-md-6">
                <input type="file" wire:model="photos" class="@error('photos') is-invalid @enderror  @error('photos.*') is-invalid @enderror" multiple>
                @error('photos.*') 
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                @error('photos') 
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                
                <!-- Progress Bar -->
                <div x-show="isUploading" class="col-md-12">
                    <progress max="100" x-bind:value="progress"></progress>
                </div>
                <p>
                    @if($this->photos)
                        <p><a style="color:rgb(206, 57, 37)" href="" wire:click="eliminarTemporales">Eliminar Archivos Temporales</a></p>
                        @foreach($this->photos as $photo)
                            @php $extension=explode(".",$photo->store('')); @endphp
                            @if($extension[1]!="pdf" && $extension[1]!="docx" && $extension[1]!="txt")
                                <img style="display:inline-block;" width="100" src="{{ $photo->temporaryUrl() }}">
                            @endif
                        @endforeach
                    @endif
                </p>
                {{-- Fotos de la base de datos 2  --}}
                @if($this->photosBD)
                    <ul wire:sortable="ordenar" class="d-flex">    
                        @foreach($this->photosBD as $key=>$photoBD)
                            <li style="margin-top: 5px" wire:sortable.item="{{ $photoBD->id }}" wire:key="photoBD-{{ $photoBD->id }}">
                                <div style="border: dashed lightslategray 1.5px" class="d-flex mx-1 my-2">
                                    <div class="p-2">
                                        <img wire:click="eliminarPhoto({{ $photoBD }})" style="cursor:pointer" width="12px" src="{{asset('imagenes/admin/eliminar.png')}}">
                                        <img style="margin-right:5px" src="{{ Storage::url('photos/'.$photoBD->nombre) }}" alt="{{ $photoBD->nombre }}" width="100">
                                    </div>
                                    <div style="cursor:pointer" wire:sortable.handle class="p-2">
                                        <img src="{{ asset('imagenes/admin/drag-drop.png') }}" alt="" width="30px">
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>      
        </div>

        <div class="form-group row">
            <label for="password_old" class="col-md-4 col-form-label text-md-right">Antigua Contraseña</label>

            <div class="col-md-6">
                <input id="password_old" wire:model.debounce.500ms="password_old" type="password" class="form-control @error('password_old') is-invalid @enderror" name="password_old"  placeholder="Introduce tu vieja contraseña para actualizar tus datos" required>
                @error('password_old')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">Nueva Contraseña</label>

            <div class="col-md-6">
                <input id="password" wire:model.debounce.500ms="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Déjalo vacío si no deseas cambiar la contraseña">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirmar Nueva contraseña</label>

            <div class="col-md-6">
                <input id="password-confirm" wire:model.debounce.500ms="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" type="password" class="form-control" name="password_confirmation" placeholder="Déjalo vacío si no deseas cambiar la contraseña">
                @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <div wire:loading.delay wire:target="submit">
                        <span style="width:20px;height:20px" class="spinner-grow text-success"></span>
                    </div>
                    Guardar
                </button>
            </div>
        </div>
    </form>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
    @endpush
</div>
