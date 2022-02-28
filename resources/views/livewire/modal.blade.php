<div>                    
    <!-- Modal -->
    <div class="modal fade" id="sal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if(!is_null($producto))
                        @foreach($producto->images as $imagen)
                            {{ $imagen->nombre }}
                        @endforeach
                        {{ $producto->category->nombre }}
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    @push('scripts')

    <script type="text/javascript">
        document.addEventListener('livewire:load', function () {
            Livewire.on('mostrarModalBlade', producto => {
                $('#sal').modal('show');
            })
        }) 
    </script>

    @endpush
</div>
