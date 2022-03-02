<div>
    {{-- @php session()->forget('typing') @endphp --}}
    {{-- @php dd(session()->all()); @endphp para ver todas las sesiones activas --}}
    <div class="row justify-content-center">
        <div class="p-3 mb-2"><h1 class="display-4"><mark class=" text-muted">Chat con Foto</mark></h1></div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12 offset-lg-1 col-lg-8 d-flex flex-wrap pl-12 pr-12">
            <div class="mr-2 mt-3">
                <button wire:click="chatGeneral" type="button" @if(session('receptor')==null) class="btn btn-success" @else class="btn btn-primary" @endif>
                    Chat General
                </button>
            </div>

            {{-- Estos son los botones que muestran los privados --}}
            @if(session('saludos')!=null)
                @foreach(session('saludos') as $saludo)
                    <div class="mr-2 mt-1">
                        <img wire:click="eliminarSesion('{{ $saludo }}')" style="cursor:pointer;" class="mb-0.5"  width="10px" src="{{asset('imagenes/admin/eliminar.png')}}">
                        <button wire:click="chatPrivado('{{ $saludo }}')" type="button" @if(session('receptor')!=null && session('receptor')==$saludo) class="btn btn-success" @elseif(session('naranjas')!=null && in_array($saludo,session('naranjas'))) class="btn btn-primary" @else class="btn btn-secondary" @endif>
                            {{ $saludo }} <span style="font-size:11px">@php if(session('typing')!=null && in_array($saludo,session('typing'))) echo "Escribiendo..."; @endphp</span><span class="badge bg-secondary">@if(session($saludo.'C')!=null) {{ session($saludo.'C') }} @endif</span>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    
    <div class="row justify-content-center mt-2">
        {{-- Muestra los mensajes públicos y privados del chat general --}}
        <div id="divu" style="height:500px;" class="col-9 col-lg-7 p-3 overflow-y-auto list-unstyled border">
            @if(session('receptor')==null)
                @foreach($this->mensajes as $m)
                    <li wire:key="{{ $m->id }}">
                        <span class="font-italic font-weight-bold">{{ $m->user->name }}</span> <span class="font-weight-light">[{{ $m->created_at->format('d-m-y | h:i') }}]</span>: {{ $m->mensaje }}</br></br>
                    </li>
                @endforeach
            @else
                @if(session(session('receptor'))!=null)
                    @foreach(session(session('receptor')) as $mensajePrivado)
                        {{ $mensajePrivado }}</br></br>
                    @endforeach
                @endif
            @endif
        </div>

        {{-- Mostramos los usuarios del chat --}}
        <div style="height:500px;" class="col-3 col-lg-2 p-2 ml-lg-2 overflow-auto border">
            @if($this->usuarios && session('receptor')==null)
                @foreach($this->usuarios as $key => $u)
                    <li class="d-flex flex-row" wire:key="{{ $u['id'] }}">
                        {{-- Al hacer clic sobre el nombre de algún usuario se ejecuta el método saludo que recibe el id de dicho usuario al que clicamos --}}
                        <span @if($u['id']!=auth()->user()->id) style="cursor: pointer" wire:click="saludo({{ $u['id'] }})" @endif class="font-italic font-weight-bold text-primary">{{ $u['name'] }}</span></br>
                        {{-- Si el usuario tiene una imagen mostramos una imagen de camara de fotos para saber que tiene imágenes --}}
                        @if($this->usuarios[$key]['images'])
                            <img class="ml-auto" width="23px" src="{{ asset('imagenes/admin/camara.png') }}" alt="icono camara fotos">
                        @endif
                    </li>
                @endforeach
            @else
                @if(isset($this->usuarioReceptor) && $this->usuarioReceptor->images()->count())
                    @foreach($this->usuarioReceptor->images as $key => $image)
                       <!-- lightbox  --->
                       <img src="{{ Storage::url('photos/'.$image->nombre) }}" alt="{{ $image->nombre }} style="width:100%" onclick="openModal();currentSlide({{ $key+1 }})" class="hover-shadow cursor mb-1">
                       <!-- lightbox  --->
                    @endforeach
                @endif
            @endif
        </div>
    </div>

    {{-- Muestra el formulario de envío de mensaje tanto publico como privado --}}
    @unless(session('receptor')!=null)
        <div class="row justify-content-center">
            <div class="col-9 col-lg-7 p-2 mt-2">
                <form wire:submit.prevent="submit" class="form-inline">
                    <div class="form-group mb-2 w-100">
                        {{-- .defer se utiliza para que no se actualicen en vivo los datos mientras voy escribiendo en el input --}}
                        <input :key="'mensaje'" wire:model.debounce="mensaje" type="text" autocomplete="off" class="form-control w-100" placeholder="Mensaje">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Enviar</button>
                </form>
            </div>
            <div class="col-3 col-lg-2"></div>
        </div>
    @else
        <div class="row justify-content-center">
            <div class="col-9 col-lg-7 p-2 mt-2">
                <form wire:submit.prevent="submitPrivado" class="form-inline">
                    <div class="form-group mb-2 w-100">
                        <input :key="'mensajePrivado'" wire:model.debounce="mensajePrivado" type="text" autocomplete="off" class="form-control w-100" placeholder="Mensaje Privado">
                    </div>
                    <button type="submitPrivado" class="btn btn-primary mb-2">Enviar</button>
                </form>
            </div>
            <div class="col-3 col-lg-2"></div>
        </div>
    @endunless


    @if(isset($this->usuarioReceptor) && $this->usuarioReceptor->images()->count())
    <!-- Modal lightbox -->
        <div id="myModal" class="modal">
            <span class="close cursor" onclick="closeModal()">&times;</span>
            <div class="modal-content">
                <!-- Abrimos bucleforeach -->
                @foreach($this->usuarioReceptor->images as $key => $image)
                    <div class="mySlides">
                        <div class="numbertext">{{ $key+1 }}</div>
                            <img src="{{ Storage::url('photos/'.$image->nombre) }}" style="width:100%">
                    </div> 
                @endforeach
                <!-- Cerramos bucleforeach -->
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
                <div class="caption-container">
                    <p id="caption"></p>
                </div>

                <!-- Abrimos bucleforeach -->
                @foreach($this->usuarioReceptor->images as $key => $image)
                    <div class="column">
                        <img class="demo cursor" src="{{ Storage::url('photos/'.$image->nombre) }}" style="width:100%" onclick="currentSlide({{ $key+1 }})" alt="{{ $image->nombre }}">
                    </div> 
                @endforeach
                <!-- Cerramos bucleforeach -->
            </div>
        </div>
    <!-- Modal lightbox -->
    @endif

    @push('styles')
    <style>
        body {
            font-family: Verdana, sans-serif;
        }

        * {
                box-sizing: border-box;
        }
        .row > .column {
                padding: 0 8px;
        }

        .row:after {
                content: "";
                display: table;
                clear: both;
        }

        .column {
                float: left;
                width: 25%;
        }
	/* The Modal (background) */
	.modal {
  		display: none;
  		position: fixed;
  		z-index: 1;
  		padding-top: 100px;
  		left: 0;
  		top: 0;
  		width: 100%;
  		height: 100%;
  		overflow: auto;
  		background-color: black;
	}

	/* Modal Content */
	.modal-content {
  		position: relative;
  		background-color: #fefefe;
  		margin: auto;
  		padding: 0;
  		width: 90%;
  		max-width: 1200px;
	}

	/* The Close Button */
	.close {
  		color: white;
  		position: absolute;
  		top: 10px;
  		right: 25px;
  		font-size: 35px;
  		font-weight: bold;
	}

	.close:hover,
	.close:focus {
  		color: #999;
  		text-decoration: none;
  		cursor: pointer;
	}

	.mySlides {
  		display: none;
	}

	.cursor {
  		cursor: pointer;
	}

	/* Next & previous buttons */
	.prev,
	.next {
  		cursor: pointer;
  		position: absolute;
  		top: 50%;
  		width: auto;
  		padding: 16px;
  		margin-top: -50px;
  		color: white;
  		font-weight: bold;
  		font-size: 20px;
  		transition: 0.6s ease;
  		border-radius: 0 3px 3px 0;
  		user-select: none;
  		-webkit-user-select: none;
	}

	/* Position the "next button" to the right */
	.next {
  	right: 0;
  	border-radius: 3px 0 0 3px;
	}

	/* On hover, add a black background color with a little bit see-through */
	.prev:hover,
	.next:hover {
  		background-color: rgba(0, 0, 0, 0.8);
	}

	/* Number text (1/3 etc) */
	.numbertext {
  		color: #f2f2f2;
  		font-size: 12px;
  		padding: 8px 12px;
  		position: absolute;
  		top: 0;
	}

	img {
  		margin-bottom: -4px;
	}

	.caption-container {
  		text-align: center;
  		background-color: black;
  		padding: 2px 16px;
  		color: white;
	}

	.demo {
  		opacity: 0.6;
	}

	.active,
	.demo:hover {
  		opacity: 1;
	}

	img.hover-shadow {
  		transition: 0.3s;
	}

	.hover-shadow:hover {
  		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
	}
    </style>
    @endpush
    @push('scripts')
        <script>
            // Pusher.logToConsole=true;
            // var pusher = new Pusher('3e68ad2c26211ad14a8f', {
            // cluster: 'mt1',
            // forceTLS:true
            // });
            // var channel = pusher.subscribe('msn');
            // channel.bind('chat-event', function(data) {
            //     alert('An event was triggered with message: ' + data.message);
            // });

            // Echo.channel('msn')
            // .listen('MensajePublico', (e) => {
            //     alert("hola");
            // });
            document.addEventListener("DOMContentLoaded", () => {    
                Livewire.on('scroll', () => {
                    // $('#divu').scrollTop( $('#divu').prop('scrollHeight') );
                    $("#divu").animate({ scrollTop: $('#divu').prop("scrollHeight")}, 1000);
                })

                Livewire.on('sonidoPrivado', () => {
                    var snd = new Audio("{{ asset('audio/saludo.mp3') }}");
                    snd.play();
                })
            });
            // Lo siguiente es para el javascript del lightbox
            function openModal() {
                document.getElementById("myModal").style.display = "block";
            }

            function closeModal() {
                document.getElementById("myModal").style.display = "none";
            }

            var slideIndex = 1;
            showSlides(slideIndex);

            function plusSlides(n) {
                showSlides(slideIndex += n);
            }

            function currentSlide(n) {
                showSlides(slideIndex = n);
            }

            function showSlides(n) {
                var i;
                var slides = document.getElementsByClassName("mySlides");
                var dots = document.getElementsByClassName("demo");
                var captionText = document.getElementById("caption");
                if (n > slides.length) {slideIndex = 1}
                if (n < 1) {slideIndex = slides.length}
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
              
                slides[slideIndex-1].style.display = "block";
                dots[slideIndex-1].className += " active";
                captionText.innerHTML = dots[slideIndex-1].alt;
            }
        </script>
    @endpush
</div>
