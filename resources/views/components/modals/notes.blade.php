<link rel="stylesheet" href="{{ asset('assets/css/components/modals.css') }}">

<div class="modal fade" id="notesModal" tabindex="-1" aria-labelledby="notesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form action="/service/pause" method="post"></form>
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="notesModalLabel">
                    <span class="main-dark">
                        Catatan
                    </span>
                    <span class="title-color">
                        Pause
                    </span>
                    <span class="main-dark">
                        | *Wajib
                    </span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-floating">
                    <input type="text" name="jobid" id="notes-id" class="d-none">
                    <textarea class="form-control" placeholder="Tinggalkan pesan untuk customer" id="notes"></textarea>
                    <label for="notes">Tinggalkan pesan untuk customer</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-save" id="btn-notes">Confirm</button>
            </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).on('click', '.btn-pause-teknisi', function(e) {
            e.preventDefault();

            let progress = this.getAttribute('data-progress');
            let jobid = this.getAttribute('data-id');
            let notes = (progress === 'Service') ? $('#notes').val() : '-';

            if (progress === 'Waiting for Service') {
                Swal.fire(
                    'Error',
                    'Status kendaraan belum mulai di servis',
                    'error'
                )
            } else {
                showConfirmation(progress, jobid, notes);
            }
        });

        $(document).on('click', '#btn-notes', function(e) {
            e.preventDefault();

            let notes = $('#notes').val();
            let jobid = $('#notes-id').val();

            savePauseStatus(jobid, notes);
        });

        function showConfirmation(progress, jobid, notes) {
            let confirmationText = (progress === 'Service') ?
                'Akan menghentikan pekerjaan untuk sementara?' : 'Akan melanjutkan pekerjaan?';

            Swal.fire({
                icon: 'question',
                title: 'Anda yakin?',
                text: confirmationText,
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    if (progress === 'Service' || progress === 'Waiting for Service') {
                        $('#notes-id').val(jobid);
                        $('#notesModal').modal('show');
                    } else {
                        savePauseStatus(jobid, notes);
                    }
                }
            });
        }

        function savePauseStatus(jobid, notes) {
            $.ajax({
                type: "POST",
                url: "{{ route('service.pause') }}",
                data: {
                    notes: notes,
                    jobid: jobid,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Record Saved!',
                        text: (notes === '-') ? 'Status service kendaraan dilanjutkan.' :
                            'Status service kendaraan ditunda.',
                    });
                    $('.table').DataTable().ajax.reload();
                    $('#notesModal').modal('hide');
                    $('#notes').val('');
                },
                error: function(xhr, error, status) {
                    console.log(xhr, status, error);
                }
            });
        }
    </script>
@endpush
