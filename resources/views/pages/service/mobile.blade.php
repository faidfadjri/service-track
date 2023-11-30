<div class="mobile">
    <div class="top">
        <h1>Service Track</h1>
        <div class="avatar-wrap">
            <img src="https://th.bing.com/th/id/OIP.WJrIBdWMZQfSlBeZpgWlqQHaHa?pid=ImgDet&rs=1" alt="avatar"
                class="avatar">
        </div>
    </div>
    <input type="text" value="{{ $role }}" class="d-none" id="session-role">
    <div class="actions">
        <div class="form-group">
            <input type="text" class="form-control" id="filter-search" placeholder="Search Nopol">
        </div>
        <button id="mobile-add" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addModal">Add</button>
    </div>
    <div class="holders" id="vehicle-lists">

    </div>
</div>

@include('components.modals.detail')
@include('components.templates.navbar')

@push('script')
    <script>
        $(document).ready(function() {
            function renderVehicleCard(data) {
                const roleElement = document.getElementById('session-role');
                const role = roleElement.value;

                let action = '';

                switch (role) {
                    case 'Service Advisor':
                    case 'Admin':
                        action = `
                            <div class="card-action">
                                <i 
                                    class="bi bi-pencil-fill detail-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal" 
                                    data-job-id="${data.id}" 
                                    data-date="${data.Tanggal}" 
                                    data-wo="${data.WO}" 
                                    data-division="${data.Division}" 
                                    data-progress="${data.Progress}" 
                                    data-nopol="${data.Nopol}" 
                                    data-model="${data.ModelType}" 
                                    data-customer="${data.CustomerName}" 
                                    data-jobtype="${data.JobType}" 
                                    data-phone="${data.Phone}" 
                                </i>
                                <i 
                                    class="bi bi-bell-fill btn-notification" 
                                    data-id=${data.id} 
                                    data-progress=${data.Progress} 
                                    data-progressid=${data.ProgressId}>
                                </i>
                                <i 
                                    class="bi bi-clipboard2-check-fill btn-finish" 
                                    data-id="${data.id}"
                                    data-progress="${data.Progress}"
                                    data-date="${data.Tanggal}"
                                    data-progressid="${data.ProgressId}">
                                </i>
                            </div>`;
                        break;
                    case 'Teknisi':
                        action = `
                            <div class="card-action">
                                <i 
                                    class="bi bi-play-fill btn-start-teknisi" 
                                    id="start-${data.id}" 
                                    data-id="${data.id}" 
                                    data-date="${data.Tanggal}" 
                                    data-progress="${data.Progress}">
                                </i>
                                <i 
                                    class="bi bi-pause-fill btn-pause-teknisi" 
                                    id="pause" 
                                    data-id="${data.id}" 
                                    data-progress=${data.Progress}>
                                </i>
                                <i 
                                    class="bi bi-skip-forward-fill btn-end-teknisi" 
                                    id="end" 
                                    data-id="${data.id}" 
                                    data-progress="${data.Progress}" 
                                    data-date="${data.Tanggal}">
                                </i>
                            </div>`;
                        break;
                    case 'Foreman':
                        action = `
                            <div class="card-action">
                                <i class="bi bi-play-fill btn-start-foreman" id="start-${data.id}" 
                                    data-id="${data.id} "
                                    data-date="${data.Tanggal}"
                                    data-progress="${data.Progress}">
                                </i>
                                <i class="bi bi-skip-forward-fill btn-end-foreman" id="end" 
                                    data-id="${data.id}" 
                                    data-progress="${data.Progress}"
                                    data-date="${data.Tanggal}">
                                </i>
                            </div>`;
                        break;
                    case 'Washing':
                        action = `
                            <div class="card-action">
                                <i class="bi bi-play-fill btn-start-washing" id="start-${data.id}" 
                                    data-id="${data.id}"
                                    data-date="${data.Tanggal}" 
                                    data-progress="${data.Progress}">
                                </i>
                                <i class="bi bi-skip-forward-fill btn-end-washing" id="end" 
                                    data-id="${data.id}" 
                                    data-progress="${data.Progress}" 
                                    data-date="${data.Tanggal}">
                                </i>
                            </div>`;
                        break;
                    case 'Billing':
                        action = `
                            <div class="card-action">
                                <i class="bi bi-cash-coin btn-end-billing action-btn" data-id="${data.id}"></i>
                            </div>`;
                        break;
                    case 'Cashier':
                        action = `
                            <div class="card-action">
                                <i class="bi bi-cash-coin btn-end-cashier action-btn" data-id="${data.id}"></i>
                            </div>`;
                        break;
                }

                return `
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="card-top">
                                <h4>${data.ModelType}</h4>
                            </div>
                            ${data.Progress}
                            <div class="row">
                                <div class="col-8">
                                    <p>Customer Name</p>
                                    <h5>${data.CustomerName}</h5>
                                </div>
                                <div class="col-4">
                                    <p>Lisence Plate</p>
                                    <h5>${data.Nopol}</h5> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <p>No. Wo</p>
                                    <h5>${data.WO}</h5>
                                </div>
                                <div class="col-4">
                                    <p>Date</p>
                                    <h5>${data.Tanggal}</h5>
                                </div>
                            </div>
                            ${action}
                        </div>
                    </div>`;
            }


            function handleFilter() {
                var searchQuery = $('#filter-search').val();

                $.ajax({
                    type: "GET",
                    url: '{{ route('service.filter') }}',
                    data: {
                        nopol: searchQuery
                    },
                    success: function(response) {
                        var vehicleList = $('#vehicle-lists');
                        vehicleList.empty();

                        if (response.length > 0) {
                            $.each(response, function(index, data) {
                                vehicleList.append(renderVehicleCard(data));
                            });
                        } else {
                            vehicleList.append('<p>No matching results found.</p>');
                        }
                    },
                    error: function(xhr, error, status) {
                        console.log(xhr, error, status);
                    }
                });
            }

            handleFilter();

            $('#filter-search').on('input', function() {
                handleFilter();
            });
        });
    </script>
@endpush
