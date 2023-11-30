<div class="table-top">
    <div class="left">
        <div class="form-group">
            <input type="text" class="form-control" id="custom-search" placeholder="Cari">
        </div>
        @if ($role === 'Service Advisor')
            <a id="add" class="btn btn-danger float-end mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus" style="font-size: 1.2em"></i>
                Tambah
            </a>
        @endif
    </div>
    <div class="date-filter">
        <div>
            <label for="startdate">Dari</label>
            <input type="date" name="startdate" id="startdate">
        </div>
        <i class="bi bi-arrow-right"></i>
        <div>
            <label for="enddate">Sampai</label>
            <input type="date" name="enddate" id="enddate">
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="progress-table" class="table table-hover">
        <thead>
            <tr>
                <th class="all" scope="col" style="text-align: center">Tanggal</th>
                <th class="all" scope="col" style="text-align: center">Nama Pelanggan</th>
                <th class="all" scope="col" style="text-align: center">WO</th>
                <th class="all" scope="col" style="text-align: center">Nomor Polisi</th>
                <th class="all" scope="col" style="text-align: center">Model</th>
                <th class="all" scope="col" style="text-align: center">Status</th>
                <th class="all" scope="col" style="text-align: center">Janji Penyerahan</th>
                <th class="all" scope="col" style="text-align: center">Action</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<div class="custom-pagination">
    <button id="prev-btn" class="btn btn-outline-secondary">Sebelumnya</button>
    <div id="page-numbers"></div>
    <button id="next-btn" class="btn btn-outline-secondary">Selanjutnya <i class="bi bi-arrow-right"></i></button>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            var table = $('#progress-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                searching: true,
                ajax: "{{ route('service.load') }}",
                columns: [{
                        data: 'Tanggal',
                        name: 'Tanggal',
                    },
                    {
                        data: 'CustomerName',
                        name: 'CustomerName'
                    },
                    {
                        data: 'WO',
                        name: 'WO'
                    },
                    {
                        data: 'Nopol',
                        name: 'Nopol'
                    },
                    {
                        data: 'ModelType',
                        name: 'ModelType'
                    },
                    {
                        data: 'Progress',
                        name: 'Progress'
                    },
                    {
                        data: 'ReleaseDate',
                        name: 'ReleaseDate'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                lengthMenu: [
                    [5, 10, 25, -1],
                    [5, 10, 25, "All"]
                ],
                pageLength: 5,
            });

            $('#custom-search').on('keyup', function() {
                table.search($(this).val()).draw();
            });

            $('#prev-btn').on('click', function() {
                table.page('previous').draw('page');
            });

            $('#next-btn').on('click', function() {
                table.page('next').draw('page');
            });

            table.on('draw', function() {
                var pageInfo = table.page.info();
                var currentPage = pageInfo.page + 1;
                var totalPages = pageInfo.pages;
                var maxDisplayPages = 5;
                var startPage = currentPage - Math.floor(maxDisplayPages / 2);
                startPage = Math.max(startPage, 1);
                var endPage = startPage + maxDisplayPages - 1;
                endPage = Math.min(endPage, totalPages);

                var pageNumbers = '';
                for (var i = startPage; i <= endPage; i++) {
                    var activeClass = (i === currentPage) ? 'active' : '';
                    pageNumbers += '<button class="page-btn ' + activeClass + '" data-page="' + i + '">' +
                        i + '</button>';
                }

                $('#page-numbers').html(pageNumbers);
            });

            $('#page-numbers').on('click', '.page-btn', function() {
                var pageNumber = $(this).data('page') - 1;
                table.page(pageNumber).draw('page');
            });

            $('#startdate, #enddate').on('change', function() {
                console.log('Date inputs changed');
                var startDate = $('#startdate').val();
                var endDate = $('#enddate').val();

                console.log('Start Date:', startDate);
                console.log('End Date:', endDate);

                table.columns('Tanggal:name').search(startDate).draw();
            });
        });
    </script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            function updateJobStatus(JobId, Date, type) {
                $.ajax({
                    url: "{{ route('service.update') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        JobId: JobId,
                        Date: Date,
                        type: type
                    },
                    success: function(response) {
                        console.log(response);
                        Swal.fire({
                            icon: 'success',
                            title: 'Bagus!',
                            text: 'Data berhasil diupdate!',
                        }).then(() => {
                            $('.table').DataTable().ajax.reload();
                        });
                    },
                    error: function(xhr, error, status) {
                        console.log(xhr, error, status);

                        var errorMessage = xhr.responseJSON.error;

                        Swal.fire({
                            icon: 'error',
                            title: 'Ooops!',
                            text: errorMessage,
                        });
                    }
                });
            }

            function confirmAction(JobId, Type, Date, Message) {
                Swal.fire({
                    icon: 'question',
                    title: 'Anda yakin?',
                    text: Message,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateJobStatus(JobId, Date, Type);
                    }
                });
            }

            function showWarning(message) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: message,
                });
            }

            function checkAndSendNotification(JobId, Date, progress, progressId) {
                if (progressId < 10) {
                    showWarning('Progress kendaraan masih di "' + progress + '"');
                } else if (progressId > 11) {
                    showWarning('Status Kendaraan Sudah di "' + progress + '"');
                } else {
                    confirmAction(JobId, 'all', Date, 'Akan mengirimkan notifikasi ke pelanggan?');
                }
            }

            function checkAndFinish(JobId, Date, progress) {
                if (progress != 'Payment Finish' && progress != 'Finish') {
                    showWarning('Progress kendaraan masih di "' + progress + '"');
                } else if (progress === 'Finish') {
                    showWarning('Status kendaraan sudah finish!');
                } else {
                    confirmAction(JobId, 'finish', Date, 'Kendaraan sudah diterima oleh pelanggan?');
                }
            }

            function update(JobId, Type, Date, Message) {
                confirmAction(JobId, Type, Date, Message);
            }

            $(document).on('click', '.btn-start-teknisi', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var progress = $(this).data('progress');
                var Date = $(this).data('date');
                var Nopol = $(this).data('nopol');

                if (progress === "Paused") {
                    Swal.fire(
                        'Oops!',
                        'Status service masih di pause',
                        'error'
                    )
                } else if (progress === "Service") {
                    Swal.fire(
                        'Tidak Bisa',
                        'Status kendaraan sudah pada tahap "Service"',
                        'warning'
                    )
                } else {
                    update(JobId, 'start', Date, 'Akan memulai servis pada kendaraan ' + Nopol + '?');
                }
            });

            $(document).on('click', '.btn-end-teknisi', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var progress = $(this).data('progress');
                var Date = $(this).data('date');

                if (progress !== "Service") {
                    Swal.fire(
                        'Oops!',
                        'Status kendaraan masih di ' + progress,
                        'error'
                    )
                } else {
                    update(JobId, 'end', Date, 'Kendaraan sudah selesai di servis?');
                }
            });

            $(document).on('click', '.btn-start-foreman', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                var Progress = $(this).data('progress');

                if (Progress !== 'Waiting for Inspection') {
                    Swal.fire(
                        'Error',
                        'Status kendaraan sudah di ' + '"' + Progress + '"',
                        'error'
                    )
                } else {
                    update(JobId, 'start', Date, 'Akan memulai inspeksi?');
                }
            });

            $(document).on('click', '.btn-end-foreman', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                var Progress = $(this).data('progress');

                if (Progress !== 'Inspection') {
                    Swal.fire(
                        'Error',
                        'Status kendaraan masih di ' + Progress,
                        'error'
                    )
                } else {
                    update(JobId, 'end', Date, 'Kendaraan sudah selesai di inspeksi?');
                }
            });

            $(document).on('click', '.btn-start-washing', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                var Progress = $(this).data('progress');

                if (Progress !== 'Waiting for Washing') {
                    Swal.fire(
                        'Error',
                        'Status kendaraan sudah di ' + '" ' + Progress + ' "',
                        'error'
                    )
                } else {
                    update(JobId, 'start', Date, 'Akan memulai pencucian kendaraan?');
                }
            });

            $(document).on('click', '.btn-end-washing', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                var Progress = $(this).data('progress');

                if (Progress !== 'Washing') {
                    Swal.fire(
                        'Error',
                        'Status kendaraan masih di ' + '"' + Progress + '"',
                        'error'
                    )
                } else {
                    update(JobId, 'end', Date, 'Kendaraan sudah selesai di cuci?');
                }
            });

            $(document).on('click', '.btn-end-billing', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                update(JobId, 'all', Date, 'Kendaraan sudah selesai di billing?');
            });

            $(document).on('click', '.btn-end-cashier', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                update(JobId, 'all', Date, 'Kendaraan sudah selesai di bayar?');
            });

            $(document).on('click', '.btn-notification', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                var progress = $(this).data('progress');
                var progressId = $(this).data('progressid');
                console.log("progress" + progressId);
                checkAndSendNotification(JobId, Date, progress, progressId);
            });

            $(document).on('click', '.btn-finish', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');
                var Date = $(this).data('date');
                var Progress = $(this).data('progress');
                console.log("progress: " + Progress);
                checkAndFinish(JobId, Date, Progress);
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'Akan menghapus data?',
                    icon: 'question',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: '{{ route('service.delete') }}',
                            data: {
                                id: JobId,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                console.log(response);
                                Swal.fire(
                                    'Bagus',
                                    'Data berhasil dihapus!',
                                    'success'
                                )

                                $('.table').DataTable().ajax.reload();
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-cancel-job', function(e) {
                e.preventDefault();
                var JobId = $(this).data('id');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: 'Akan cancel data?',
                    icon: 'question',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "PUT",
                            url: '{{ route('service.cancel') }}',
                            data: {
                                id: JobId,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(response) {
                                console.log(response);
                                Swal.fire(
                                    'Bagus',
                                    'Data berhasil cancel!',
                                    'success'
                                )

                                $('.table').DataTable().ajax.reload();
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush
