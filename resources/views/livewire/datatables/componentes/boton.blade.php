<button type="button" wire:click="alertDeleteChecked" class="btn btn-danger">Eliminar {{ count($selected) }} registros</button>
<a style="text-decoration: none;" href="{{ route('productos.create') }}"><button type="button" class="btn btn-success">Crear Nuevo Producto</button></a>

@push('scripts')

<script type="text/javascript">

    Livewire.on('alertDeleteChecked', select => {
        Swal.fire({
            title: 'Are You Sure?',
            text: 'Order record will be deleted!',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: 'var(--success)',
            cancelButtonColor: 'var(--primary)',
            confirmButtonText: 'Delete!'
        }).then((result) => {
    //if user clicks on delete
            if (result.value) {
                //calling destroy method to delete
                Livewire.emit('deleteChecked'),
                //success response con tostada
                toastr.success("hola","Espere mientras se eliminan todos los artículos", {
                    "closeButton": false,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "positionClass": "toast-top-center",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                });
            } 
        });
    });
</script>

@endpush