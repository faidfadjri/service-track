<link rel="stylesheet" href="{{ asset('assets/css/components/modals.css') }}">

<div class="modal fade" id="addDataModal" tabindex="-1" aria-labelledby="addDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addDataModalLabel">
                    <span class="main-dark">
                        Tambah
                    </span>
                    <span class="title-color">
                        data
                    </span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="mb-3">
                            <label for="nopol" class="form-label modal-label">Nomor Polisi</label>
                            <input required type="text" class="form-control modal-input" placeholder="EX : BI986EA"
                                name="nopol" id="nopol">
                        </div>
                        <div class="mb-3">
                            <label for="model" class="form-label modal-label">Model Kendaraan</label>
                            <select required name="model" id="model" class="form-select">
                                <option value="">Select Model</option>
                                @foreach ($model as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jobtype" class="form-label modal-label">Tipe Pekerjaan</label>
                            <select required name="jobtype" id="jobtype" class="form-select">
                                <option value="">Select Job</option>
                                @foreach ($jobtype as $job)
                                    <option value="{{ $job }}">{{ $job }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="mb-3">
                            <label required for="customer" class="form-label modal-label">Nama Pelanggan</label>
                            <input type="text" class="form-control modal-input" placeholder="EX : Yayan Ruhiyan"
                                name="customer" id="customer" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label modal-label">No. Handphone</label>
                            <input required type="text" class="form-control modal-input"
                                placeholder="EX : 0876382136234" name="phone" id="phone">
                        </div>
                        <div class="mb-3">
                            <label for="customer" class="form-label modal-label">Janji Penyerahan</label>
                            <input required type="datetime-local" class="form-control modal-input" name="date"
                                id="date">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="wo" class="form-label modal-label">No. WO</label>
                            <input required type="text" class="form-control modal-input"
                                placeholder="EX : 1G0/WOG/2010-000001" name="wo" id="wo">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-save" id="btn-save-data">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#btn-save-data').click(function(e) {
            e.preventDefault();

            var customer = $('#customer').val();
            var nopol = $('#nopol').val();
            var model = $('#model').val();
            var date = $('#date').val();
            var jobtype = $('#jobtype').val();
            var phone = $('#phone').val();
            var wo = $('#wo').val();

            $.ajax({
                url: "{{ route('admin.store') }}",
                type: "POST",
                data: {
                    customer: customer,
                    nopol: nopol,
                    model: model,
                    date: date,
                    jobtype: jobtype,
                    phone: phone,
                    wo: wo,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Bagus!',
                        text: 'Data berhasil ditambahkan 🥳',
                    }).then(function() {
                        $('.table').DataTable().ajax.reload();
                        $('#addDataModal').modal('hide');
                        $('input').val('');
                    });
                },
                error: function(xhr, error, status) {
                    console.log(xhr, error, status);

                    var errorMessage = xhr.responseJSON.error;

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: errorMessage + '😵‍💫',
                    });
                }
            });
        });
    </script>
@endpush
