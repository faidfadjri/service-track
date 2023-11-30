<link rel="stylesheet" href="{{ asset('assets/css/components/modals.css') }}">

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editForm" action="/admin/store" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="detailModalLabel">
                        <span class="main-dark">
                            Edit
                        </span>
                        <span class="title-color">
                            Data
                        </span>
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <input type="text" id="detail-jobid" name="jobid" class="d-none">
                            <input type="text" id="detail-vehicleid" name="vehicleid" class="d-none">
                            <input type="text" id="detail-customerid" name="customerid" class="d-none">
                            <div class="mb-3">
                                <label for="nopol" class="form-label modal-label">Nomor Polisi</label>
                                <input required type="text" class="form-control modal-input"
                                    placeholder="EX : BI986EA" name="nopol" id="detail-nopol">
                            </div>
                            <div class="mb-3">
                                <label for="model" class="form-label modal-label">Model Kendaraan</label>
                                <select required name="model" id="detail-model" class="form-select">
                                    <option value="">Select Model</option>
                                    @foreach ($model as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jobtype" class="form-label modal-label">Tipe Pekerjaan</label>
                                <select required name="jobtype" id="detail-jobtype" class="form-select">
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
                                    name="customer" id="detail-customer" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label modal-label">No. Handphone</label>
                                <input required type="text" class="form-control modal-input"
                                    placeholder="EX : 0876382136234" name="phone" id="detail-phone">
                            </div>
                            <div class="mb-3">
                                <label for="detail-date" class="form-label modal-label">Janji Penyerahan</label>
                                <input required type="datetime-local" class="form-control modal-input" name="date"
                                    id="detail-date">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="wo" class="form-label modal-label">No. WO</label>
                                <input required type="text" class="form-control modal-input"
                                    placeholder="EX : 1G0/WOG/2010-000001" name="wo" id="detail-wo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-save" id="btn-save">
                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('script')
    <script>
        $(document).on('click', '.detail-btn', function() {
            var jobId = this.getAttribute('data-job-id');
            var customerId = this.getAttribute('data-customer-id');
            var vehicleId = this.getAttribute('data-vehicle-id');
            var datetime = this.getAttribute('data-date');
            var date = datetime.split(' ')[0];
            var wo = this.getAttribute('data-wo');
            var division = this.getAttribute('data-division');
            var progress = this.getAttribute('data-progress');
            var nopol = this.getAttribute('data-nopol');
            var model = this.getAttribute('data-model');
            var customer = this.getAttribute('data-customer');
            var jobtype = this.getAttribute('data-jobtype');
            var phone = this.getAttribute('data-phone');

            var formattedDatetime = datetime.toLocaleString('en-CA', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });

            console.log(formattedDatetime);

            $('#detail-jobid').val(jobId);
            $('#detail-customerid').val(customerId);
            $('#detail-vehicleid').val(vehicleId);
            $('#detail-customer').val(customer);
            $('#detail-nopol').val(nopol);
            $('#detail-model').val(model);
            $('#detail-date').val(formattedDatetime);
            $('#detail-jobtype').val(jobtype);
            $('#detail-phone').val(phone);
            $('#detail-wo').val(wo);

        });
    </script>
@endpush

@push('script')
    <script>
        $(document).on('submit', '#editForm', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Anda Yakin?',
                text: 'Anda akan menyimpan perubahan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'yes'
            }).then((result) => {
                if (result.isConfirmed) {

                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        type: "PUT",
                        url: '{{ route('admin.update') }}',
                        data: $(this).serialize(),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            $('.table').DataTable().ajax.reload();
                            $('#detailModal').modal('hide');
                            Swal.fire(
                                'Good',
                                'Data Updated Successfully!',
                                'success'
                            )
                        },
                        error: function(xhr, error, status) {
                            console.log(xhr, error, status);
                        }
                    });
                }
            })
        });
    </script>
@endpush
