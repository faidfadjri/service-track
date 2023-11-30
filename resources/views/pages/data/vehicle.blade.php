@extends('app')
<link rel="stylesheet" href="{{ asset('assets/css/pages/admin.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/mobile/service.css') }}">
@section('content')
    <h2 class="page-title mb-5">Data Kendaraan</h2>
    @include('components.datatables.vehicle')
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            $(document).on('click', '.btn-delete-vehicle', function(e) {
                e.preventDefault();

                var id = $(this).data('id');

                Swal.fire({
                    title: 'Anda yakin?',
                    text: 'Semua data yang berkaitan dengan kendaraan ini akan ikut terhapus.',
                    icon: 'question',
                    showCancelButton: true,
                }).then((result) => {

                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('vehicle.delete') }}',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                console.log('Ajax success:', response);
                                $('.table').DataTable().ajax.reload();
                                Swal.fire(
                                    'Bagus!',
                                    'Data kendaraan berhasil dihapus',
                                    'success'
                                )
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr, status, error);

                                Swal.fire(
                                    'Error',
                                    error,
                                    'error'
                                )
                            }
                        });
                    }
                })
            });
        })
    </script>
@endpush
