<div class="table-top">
    <div class="left">
        <div class="form-group">
            <input type="text" class="form-control" id="custom-search" placeholder="Cari">
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="progress-table" class="table table-hover">
        <thead>
            <tr>
                <th class="all" scope="col" style="text-align: center">#</th>
                <th class="all" scope="col" style="text-align: center">Nomor Polisi</th>
                <th class="all" scope="col" style="text-align: center">Model Kendaraan</th>
                <th class="all" scope="col" style="text-align: center">Nama Pemilik</th>
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
                ajax: "{{ route('vehicle.load') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                    },
                    {
                        data: 'LisencePlate',
                        name: 'LisencePlate',
                    },
                    {
                        data: 'ModelType',
                        name: 'ModelType'
                    },
                    {
                        data: 'customer.CustomerName',
                        name: 'customer.CustomerName'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
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
        });
    </script>
@endpush
