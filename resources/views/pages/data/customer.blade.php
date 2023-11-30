@extends('app')

@section('content')
    <h2 class="page-title mb-5">Data Pelanggan</h2>
    @include('components.datatables.customer')
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            $(document).on('click', '.btn-delete-customer', function(e) {
                e.preventDefault();

                var id = $(this).data('id');

                Swal.fire({
                    title: 'Anda yakin?',
                    text: 'Semua data yang berkaitan dengan pelanggan ini akan ikut terhapus.',
                    icon: 'question',
                    showCancelButton: true,
                }).then((result) => {
                    console.log('SweetAlert result:', result);

                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('customer.delete') }}',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                console.log('Ajax success:', response);
                                $('.table').DataTable().ajax.reload();
                                Swal.fire(
                                    'Bagus!',
                                    'Data pelanggan berhasil dihapus',
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
