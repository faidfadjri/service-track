<link rel="stylesheet" href="{{ asset('assets/css/components/modals.css') }}">

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModalLabel">
                    <span class="main-dark">
                        Tambah
                    </span>
                    <span class="title-color">
                        Pekerjaan
                    </span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row search-wrap">
                    <div class="search-bar-wrap">
                        <input type="text" class="form-control" placeholder="Nopol" name="nopol-search"
                            id="nopol-search">
                    </div>
                    <div class="btn-add-wrap">
                        <a class="btn btn-red" data-bs-toggle="modal" data-bs-target="#addDataModal"
                            id="btn-addDataModal">
                            <i class="bi bi-plus"></i>
                            Tambah
                        </a>
                    </div>
                </div>
                <div id="vehicle-list" class="customer-list">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-save" style="width: 100%" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            const vehicleList = $('#vehicle-list');

            function handleSearch() {
                const searchQuery = $('#nopol-search').val();

                $.ajax({
                    url: '{{ route('admin.search') }}',
                    method: 'GET',
                    data: {
                        nopol: searchQuery
                    },
                    success: function(response) {
                        vehicleList.empty();

                        if (response.vehicles.length === 0) {
                            const noDataHtml = `
                                <div style="display: flex; justify-content: center; align-items: center">
                                    <h3>Data tidak ditemukan</h3>
                                </div>
                            `;
                            vehicleList.append(noDataHtml);
                        } else {
                            response.vehicles.forEach(function(vehicle) {
                                const listItemHtml = `
                                    <div class="list">
                                        <div class="customer-wrap">
                                            <div class="avatar-wrap">
                                                <img src="/assets/img/car.png" 
                                                    alt="avatar"
                                                    class="avatar"
                                                >
                                            </div>
                                            <div class="name-wrap">
                                                <span class="customer-name">${vehicle.customer.CustomerName}</span>
                                                <span class="customer-nopol">${vehicle.LisencePlate}</span>
                                            </div>
                                        </div>
                                        <div 
                                            class="add-wo" 
                                            id="add-wo-${vehicle.LisencePlate}" 
                                            data-bs-dismiss="modal"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#addWoModal"
                                            data-nopol="${vehicle.LisencePlate}"
                                            data-vehicleid="${vehicle.id}"
                                        >
                                            <i class="bi bi-plus" style="font-weight: 600"></i>
                                        </div>
                                    </div>
                                `;
                                vehicleList.append(listItemHtml);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr, error, status);
                    }
                });
            }

            $('#nopol-search').on('input', handleSearch);

            $('#add').on('click', handleSearch);
            $('#mobile-add').on('click', handleSearch);

            $(document).on('click', '[id^="add-wo-"]', function(e) {
                e.preventDefault();

                $('input').val('');

                const nopol = $(this).data('nopol');
                const vehicleid = $(this).data('vehicleid');

                $('#wo-nopol').val(nopol);
                $('#wo-vehicleid').val(vehicleid);

                $('#addModal').modal('hide');
                $('#addWoModal').modal('show');
            });

            $('#btn-addDataModal').click(function(e) {
                $('#addModal').modal('hide');
            });
        });
    </script>
@endpush
