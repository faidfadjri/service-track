<link rel="stylesheet" href="{{ asset('assets/css/components/modals.css') }}">

<div class="modal fade" id="addWoModal" tabindex="-1" aria-labelledby="addWoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                    <span class="main-dark">Tambah</span>
                    <span class="title-color">WO</span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="d-none" name="nopol" id="wo-nopol">
                <input type="text" class="d-none" name="vehicleid" id="wo-vehicleid">
                <div class="mb-3">
                    <label for="nowo" class="form-label modal-label">Nomor WO</label>
                    <input type="text" class="form-control" placeholder="Masukan nomor WO" name="nowo"
                        id="nowo">
                </div>
                <div class="mb-3">
                    <label for="wo-date" class="form-label modal-label">Janji Penyerahan</label>
                    <input type="datetime-local" class="form-control" name="date" id="wo-date">
                </div>
                <div class="mb-3">
                    <label for="wo-jobtype" class="form-label modal-label">Tipe Pekerjaan</label>
                    <select required name="jobtype" id="wo-jobtype" class="form-select">
                        <option value="">Select Job</option>
                        @foreach ($jobtype as $job)
                            <option value="{{ $job }}">{{ $job }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-save btn-save-wo" id="btn-save-wo">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#btn-save-wo').click(function() {
            var wo = $('#nowo').val();
            var nopol = $('#wo-nopol').val();
            var vehicleid = $('#wo-vehicleid').val();
            var date = $('#wo-date').val();
            var jobtype = $('#wo-jobtype').val();

            $.ajax({
                url: "{{ route('admin.addjob') }}",
                type: "POST",
                data: {
                    wo: wo,
                    nopol: nopol,
                    vehicleid: vehicleid,
                    date: date,
                    jobtype: jobtype,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Bagus!',
                        text: 'Data berhasil ditambahkan 🥳',
                    }).then(function() {
                        $('.table').DataTable().ajax.reload();
                        $('#addWoModal').modal('hide');
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr, status, error);

                    var errorMessage = "";
                    var errorObj = xhr.responseJSON.error;

                    if (typeof errorObj === 'object') {
                        for (var key in errorObj) {
                            if (errorObj.hasOwnProperty(key)) {
                                errorMessage += errorObj[key] + "\n";
                            }
                        }
                    } else {
                        errorMessage = errorObj;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: errorMessage + ' 🤕',
                    });
                }

            });
        });
    </script>
@endpush
