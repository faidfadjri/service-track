@extends('app')

<link rel="stylesheet" href="{{ asset('assets/css/pages/dashboard.css') }}">

@section('content')
    <div class="dashboard-main">
        <div class="header mb-3">
            <div class="logo-wrap">
                <img class="logo" src="{{ asset('assets/img/logo.png') }}" alt="Logo">
                <span class="title">Akastra Service Track</span>
            </div>
            <div class="timestamp">
                <h3 id="timer"></h3>
                <h5 id="date"><small></small></h5>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr class="dashboard-tr">
                    <th scope="col" style="text-align: center">License Plate</th>
                    <th scope="col" style="text-align: center">Model Type</th>
                    <th scope="col" style="text-align: center">Customer Name</th>
                    <th scope="col" style="text-align: center">Date</th>
                    <th scope="col" style="text-align: center">Progress</th>
                </tr>
            </thead>
            <tbody id="progress-table">

            </tbody>
        </table>
        <div class="pagination-buttons">
            <button id="prevPageBtn">Previous</button>
            <button id="nextPageBtn">Next</button>
        </div>
    </div>
@endsection

@push('script')
    <script>
        var currentPage = 2;
        var itemsPerPage = 10;

        function updateTimestamp() {
            var currentDateTime = new Date();
            var formattedTime = currentDateTime.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            var formattedDate = currentDateTime.toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'long',
                day: '2-digit'
            });

            $('#timer').text(formattedTime);
            $('#date small').text(formattedDate);
        }

        function updateProgress() {
            $.ajax({
                url: "{{ route('dashboard.data') }}",
                method: 'GET',
                success: function(response) {
                    var startIndex = (currentPage - 1) * itemsPerPage;
                    var endIndex = startIndex + itemsPerPage;

                    var slicedResponse = response.slice(startIndex, endIndex);

                    var total = response.length;
                    var data = total > 10 ? slicedResponse : response;

                    var tableBody = data.map(item => {
                        return `
                        <tr class="dashboard-tr">
                            <td>${item.Nopol}</td>
                            <td>${item.ModelType}</td>
                            <td>${item.CustomerName}</td>
                            <td>${item.Tanggal}</td>
                            <td class="progress-column" data-progress="${item.id}">${item.Progress}</td>
                        </tr>
                    `;
                    }).join('');

                    $('#progress-table').html(tableBody);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }

        setInterval(function() {
            updateTimestamp();
            updateProgress();
        }, 1000);

        // Handle next page click
        $('#nextPageBtn').on('click', function() {
            currentPage++;
            updateProgress();
        });

        // Handle previous page click
        $('#prevPageBtn').on('click', function() {
            if (currentPage > 1) {
                currentPage--;
                updateProgress();
            }
        });
    </script>
@endpush
