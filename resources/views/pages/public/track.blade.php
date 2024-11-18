@extends('app')
<link rel="stylesheet" href="{{ asset('assets/css/pages/track.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/components/footer.css') }}">
@section('content')
    <div class="main-search" style="position: relative;">
        <div class="top">
            <div class="top-wrap">
                <div class="logo-wrap">
                    <img class="logo" src={{ asset('assets/img/logo.png') }}></img>
                    <span class="title">Service Tracking</span>
                </div>

                <p class="desc">Masukan Nopol dan No.Wo</p>
                <div class="search-bar">
                    <input type="text" class="form-control" placeholder="Masukan Nopol:No.Wo" name="data"
                        id="data">
                    <div class="btn-wrap">
                        <button class="btn btn-danger btn-search">
                            <i class="bi bi-search"></i>
                            Lacak
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="results" class="d-flex justify-content-center align-items-center">

        </div>
    </div>

    <div class="result">

    </div>

    @include('components.services.description')
    @include('components.modals.track')
    @include('components.modals.add')
    @include('components.templates.footer')
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showErrorAlert = (message) => {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK',
                });
            };

            document.querySelector('.btn-search').addEventListener('click', function() {
                const inputData = document.getElementById('data').value.trim();

                if (inputData === '') {
                    showErrorAlert('Masukan nomor polisi dan nomor work order terlebih dahulu!');
                } else if (!inputData.includes(':')) {
                    showErrorAlert('Pastikan format data anda benar (nopol:wo)');
                } else {
                    const data = $('#data').val();

                    $('#results').html('<div class="spinner-border text-primary" role="status"></div>');

                    $.ajax({
                        url: "{{ route('track.search') }}",
                        type: "POST",
                        data: {
                            data: data,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            const validProgressValues = [
                                'Reception', 'Service', 'Inspection', 'Washing', 'Billing',
                                'Notification', 'Payment Finish', 'Finish'
                            ];

                            const filteredResponse = response.filter(item => validProgressValues
                                .includes(item.Progress));

                            filteredResponse.sort((a, b) => {
                                const dateA = new Date(a.ClockOnAt);
                                const dateB = new Date(b.ClockOnAt);

                                if (dateA.getTime() === dateB.getTime()) {
                                    return a.ClockOnAt.localeCompare(b.ClockOnAt);
                                }

                                return dateA - dateB;
                            });

                            console.log(filteredResponse);

                            let detailProgressHtml = "";
                            const checkpoint = [
                                'Mobil Masuk', 'Teknisi Melakukan Perbaikan',
                                'Foreman Melakukan Inspeksi dan Final Test',
                                'Kendaraan Sedang di Cuci',
                                'Kendaraan Sudah di Billing',
                                'Notifikasi dikirmkan ke pelanggan',
                                'Pelanggan Melakukan Pembayaran',
                                'Kendaraan Sudah diterima Oleh Pelanggan'
                            ];

                            filteredResponse.forEach((index, i) => {
                                const currentCheckpoint = checkpoint[i] ||
                                    'Unknown Checkpoint';
                                const clockOffHtml = index.ClockOffAt !== null ?
                                    `<small> - ${index.ClockOffAt}</small>` : '';

                                const current = filteredResponse[i].Progress;

                                if (current !== 'Billing' &&
                                    current !== 'Notification' &&
                                    current !== 'Payment Finish' &&
                                    current !== 'Finish') {
                                    detailProgressHtml += `
                                        <div class="points">
                                            <i class="bi bi-circle-fill"></i>
                                            <div>
                                            <p class="current">${currentCheckpoint}</p>
                                            <p><small>${index.ClockOnAt}</small>${clockOffHtml}</p>
                                            </div>
                                        </div>
                                    `;
                                } else {
                                    detailProgressHtml += `
                                        <div class="points">
                                            <i class="bi bi-circle-fill"></i>
                                            <div>
                                            <p class="current">${currentCheckpoint}</p>
                                            <p><small>${index.ClockOnAt}</small></p>
                                            </div>
                                        </div>
                                    `;
                                }


                            });

                            if (response.length !== 0) {
                                console.log(response[0].Progress);

                                const progress = response[0].ProgressId - 2;
                                console.log(progress);
                                const increments = [10, 17, 24, 31, 37, 44, 52, 57, 66, 74, 82,
                                    88, 95, 100
                                ];
                                const bar = increments[progress] || 0;

                                var cardHtml = `
                                    <div class="data">
                                        <div class="badge-wrap">
                                            <span class="wo-badge">${response[0].LisencePlate}</span>
                                            <span class="status-badge">${response[0].WO}</span>
                                        </div>
                                        <div class="header mb-3">
                                            <div class="judul">
                                                <i class="fa fa-arrow-circle-o-left" aria-hidden="true"></i>
                                                <span>Data Servis</span>
                                            </div>
                                            <div class="estimation">
                                                <span>Estimasi Selesai</span>
                                                <p>${response[0].ReleaseDate}</p>
                                            </div>
                                        </div>
                                        <div class="data-kendaraan">
                                            <div class="col">
                                                <span>No Polisi</span>
                                                <p>${response[0].LisencePlate}</p>
                                            </div>
                                            <div class="col">
                                                <span>No Work Order</span>
                                                <p>${response[0].WO}</p>
                                            </div>
                                            <div class="col">
                                                <span>Service Advisor</span>
                                                <p>${response[0].fullname}</p>
                                            </div>
                                        </div>
                                        <span class="judul-status">Status Servis Kendaraan</span>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: ${bar}%" aria-valuenow="${response.progress}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="history mt-5">
                                            <div class="chain">
                                                <div class="wrap reception one check">
                                                    <div class="checkpoint one check">
                                                        <i class="bi bi-play-fill one check"></i>
                                                    </div>
                                                    <p>Reception</p>
                                                </div>
                                                <i class="bi bi-circle-fill two" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting for Service"></i>
                                                <div class="wrap service three">
                                                    <div class="checkpoint three">
                                                        <i class="bi bi-tools three"></i>
                                                    </div>
                                                    <p>Service</p>
                                                </div>
                                                <i class="bi bi-circle-fill four" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting for Inspection"></i>
                                                <div class="wrap inspection five">
                                                    <div class="checkpoint five">
                                                        <i class="bi bi-journal-text five"></i>
                                                    </div>
                                                    <p>Inspection</p>
                                                </div>
                                                <i class="bi bi-circle-fill six" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting for Washing"></i>
                                                <div class="wrap washing seven">
                                                    <div class="checkpoint seven">
                                                        <i class="bi bi-car-front seven"></i>
                                                    </div>
                                                    <p>Washing</p>
                                                </div>
                                                <i class="bi bi-circle-fill eight" data-bs-toggle="tooltip" data-bs-placement="top" title="Waitingb for Billing"></i>
                                                <div class="wrap billing nine">
                                                    <div class="checkpoint nine">
                                                        <i class="bi bi-receipt-cutoff nine"></i>
                                                    </div>
                                                    <p>Billing</p>
                                                </div>
                                                <i class="bi bi-circle-fill ten" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting for Notification"></i>
                                                <div class="wrap ready eleven">
                                                    <div class="checkpoint eleven">
                                                        <i class="bi bi-bell eleven"></i>
                                                    </div>
                                                    <p>Ready for Delivery</p>
                                                </div>
                                                <i class="bi bi-circle-fill twelve" class="color: white;" data-bs-toggle="tooltip" data-bs-placement="top" title="Waiting for Paymentt"></i>
                                                <div class="wrap payment thirtheen">
                                                    <div class="checkpoint thirtheen">
                                                        <i class="bi bi-wallet2 thirtheen"></i>
                                                    </div>
                                                    <p>Payment</p>
                                                </div>
                                                <div class="wrap finish fourteen">
                                                    <div class="checkpoint fourteen">
                                                        <i class="bi bi-journal-check fourteen"></i>
                                                    </div>
                                                    <p>Finish</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="detail-progress">
                                            <div class="line">
                                                ${detailProgressHtml}
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $('#results').html(cardHtml).hide().fadeIn();
                            } else {
                                $('.spinner-border').addClass('d-none');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pencarian Gagal',
                                    text: 'Data tidak ditemukan',
                                });
                            }
                        },
                        error: function(xhr, error, status) {
                            console.log(xhr, error, status);
                            $('.spinner-border').addClass('d-none');
                            Swal.fire({
                                icon: 'error',
                                title: 'Pencarian Gagal',
                                text: error,
                                footer: 'footerText',
                            });
                        },
                    }).done(function(response) {
                        const progress = response[0].ProgressId;
                        const classes = ['.one', '.two', '.three', '.four', '.five', '.six',
                            '.seven', '.eight', '.nine', '.ten', '.eleven', '.twelve',
                            '.thirtheen', '.fourteen'
                        ];

                        for (let i = 0; i < progress; i++) {
                            $(classes[i]).addClass('check');
                        }

                        $('#btn-track').click(function(e) {
                            $('#trackModal').modal('hide');
                        });
                    });
                }
            });
        });
    </script>
@endpush

{{-- @push('script')
    <!-- Include SweetAlert2 CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    </link>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function showErrorAlert() {
                Swal.fire({
                    icon: 'error',
                    title: 'Pencarian Gagal',
                    text: 'Masukan nomor polisi dan nomor work order terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
            }

            document.querySelector('.btn-search').addEventListener('click', function() {
                var inputData = document.getElementById('data').value;

                if (inputData.trim() === '') {
                    showErrorAlert();
                } else {
                    var data = $('#data').val();

                    $('#results').html('<div class="spinner-border text-primary" role="status"></div>');

                    $.ajax({
                        url: "{{ route('track.search') }}",
                        type: "POST",
                        data: {
                            data: data,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            const validProgressValues = ['Reception', 'Service', 'Inspection',
                                'Washing', 'Billing', 'Notification', 'Cashier', 'Finish'
                            ];

                            const filteredResponse = response.filter(item => validProgressValues
                                .includes(item.Progress));

                            filteredResponse.sort(function(a, b) {
                                const dateA = new Date(a.ClockOnAt);
                                const dateB = new Date(b.ClockOnAt);

                                // If dates are equal, compare using the datetime string for more precision
                                if (dateA.getTime() === dateB.getTime()) {
                                    return a.ClockOnAt.localeCompare(b.ClockOnAt);
                                }

                                return dateA - dateB;
                            });


                            var detailProgressHtml = "";
                            var checkpoint = [
                                'Mobil Masuk',
                                'Teknisi Melakukan Perbaikan',
                                'Foreman Melakukan Inspeksi dan Final Test',
                                'Kendaraan Sedang di Cuci',
                                'Kendaraan Sudah di Billing',
                                'Pelanggan Melakukan Pembayaran',
                                'Kendaraan Sudah diterima Oleh Pelanggan'
                            ];

                            filteredResponse.forEach(function(index, i) {
                                var currentCheckpoint = checkpoint[i] ||
                                    'Unknown Checkpoint';
                                var clockOffHtml = index.ClockOffAt !== null ?
                                    `<small> - ${index.ClockOffAt}</small>` :
                                    '';

                                detailProgressHtml += `
                <div class="points">
                    <i class="bi bi-circle-fill"></i>
                    <div>
                        <p class="current">${currentCheckpoint}</p>
                        <p><small>${index.ClockOnAt}</small>${clockOffHtml}</p>
                    </div>
                </div>`;
                            });


                            if (response) {

                                console.log(response[0].Progress);

                                let progress = response[0].ProgressId - 2;
                                let increments = [10, 17, 24, 31, 39, 44, 53, 57, 66, 74, 82,
                                    88,
                                    95, 100
                                ];

                                console.log("Progress: " + progress)

                                let bar = increments[progress] || 0;

                                var cardHtml = `
                <div class="bottom">
                    <div class="card trackmodal" data-bs-toggle="modal" data-bs-target="#trackModal">
                        <div class="card-body">
                            <span class="division">${response[0].Division}</span>
                            <h1 class="card-name">${response[0].CustomerName}</h1>
                            <h5 class="card-detail">${response[0].ModelType}</h5>
                            <h5 class="card-detail">${response[0].LisencePlate}</h5>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: ${bar}%" aria-valuenow="${response[0].progress}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="history mt-5">
                                <div class="chain">
                                    <div class="wrap reception one">
                                        <div class="checkpoint one">
                                            <i class="bi bi-play-fill one"></i>
                                        </div>
                                    </div>
                                    <i class="bi bi-circle-fill two"></i>
                                    <div class="wrap service three">
                                        <div class="checkpoint three">
                                            <i class="bi bi-tools three"></i>
                                        </div>
                                    </div>
                                    <i class="bi bi-circle-fill four"></i>
                                    <div class="wrap inspection five">
                                        <div class="checkpoint five">
                                            <i class="bi bi-journal-text five"></i>
                                        </div>
                                    </div>
                                    <i class="bi bi-circle-fill six"></i>
                                    <div class="wrap washing seven">
                                        <div class="checkpoint seven">
                                            <i class="bi bi-car-front seven"></i>
                                        </div>
                                    </div>
                                    <i class="bi bi-circle-fill eight"></i>
                                    <div class="wrap billing nine">
                                        <div class="checkpoint nine">
                                            <i class="bi bi-receipt-cutoff nine"></i>
                                        </div>
                                    </div>
                                    <i class="bi bi-circle-fill ten"></i>
                                    <div class="wrap ready eleven">
                                        <div class="checkpoint eleven">
                                            <i class="bi bi-bell eleven"></i>
                                        </div>
                                    </div>
                                    <i class="bi bi-circle-fill twelve"></i>
                                    <div class="wrap payment thirtheen">
                                        <div class="checkpoint thirtheen">
                                            <i class="bi bi-wallet2 thirtheen"></i>
                                        </div>
                                    </div>
                                    <div class="wrap finish fourteen">
                                        <div class="checkpoint fourteen">
                                            <i class="bi bi-journal-check fourteen"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="mobile-chain">
                                    <div class="mobile-line"></div>
                                    <div class="wrap mobile-reception one">
                                        <div class="checkpoint one">
                                            <i class="bi bi-play-fill one"></i>
                                        </div>
                                        <p>Reception</p>
                                    </div>
                                    <div class="wrap mobile-service three">
                                        <div class="checkpoint three">
                                            <i class="bi bi-tools three"></i>
                                        </div>
                                        <p>Service</p>
                                    </div>
                                    <div class="wrap mobile-inspection five">
                                        <div class="checkpoint five">
                                            <i class="bi bi-journal-text five"></i>
                                        </div>
                                        <p>Inspection</p>
                                    </div>
                                    <div class="wrap mobile-washing seven">
                                        <div class="checkpoint seven">
                                            <i class="bi bi-car-front seven"></i>
                                        </div>
                                        <p>Washing</p>
                                    </div>
                                    <div class="wrap mobile-billing nine">
                                        <div class="checkpoint nine">
                                            <i class="bi bi-receipt-cutoff nine"></i>
                                        </div>
                                        <p>Billing</p>
                                    </div>
                                    <div class="wrap mobile-ready eleven">
                                        <div class="checkpoint eleven">
                                            <i class="bi bi-bell eleven"></i>
                                        </div>
                                        <p>Ready for Delivery</p>
                                    </div>
                                    <div class="wrap mobile-payment thirtheen">
                                        <div class="checkpoint thirtheen">
                                            <i class="bi bi-wallet2 thirtheen"></i>
                                        </div>
                                        <p>Payment</p>
                                    </div>
                                    <div class="wrap mobile-finish fourteen">
                                        <div class="checkpoint fourteen">
                                            <i class="bi bi-journal-check fourteen"></i>
                                        </div>
                                        <p>Finish</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="trackModal" tabindex="-1" aria-labelledby="trackModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="trackModalLabel">
                                <span class="main-dark">
                                    History
                                </span>
                                <span class="title-color">
                                    Service
                                </span>
                            </h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body track-modal">
                            <div class="track-top">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: ${bar}%" aria-valuenow="${response.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="history mt-5">
                                    <div class="chain">
                                        <div class="wrap reception one">
                                            <div class="checkpoint one">
                                                <i class="bi bi-play-fill one"></i>
                                            </div>
                                            <p>Reception</p>
                                        </div>
                                        <i class="bi bi-circle-fill two"></i>
                                        <div class="wrap service three">
                                            <div class="checkpoint three">
                                                <i class="bi bi-tools three"></i>
                                            </div>
                                            <p>Service</p>
                                        </div>
                                        <i class="bi bi-circle-fill four"></i>
                                        <div class="wrap inspection five">
                                            <div class="checkpoint five">
                                                <i class="bi bi-journal-text five"></i>
                                            </div>
                                            <p>Inspection</p>
                                        </div>
                                        <i class="bi bi-circle-fill six"></i>
                                        <div class="wrap washing seven">
                                            <div class="checkpoint seven">
                                                <i class="bi bi-car-front seven"></i>
                                            </div>
                                            <p>Washing</p>
                                        </div>
                                        <i class="bi bi-circle-fill eight"></i>
                                        <div class="wrap billing nine">
                                            <div class="checkpoint nine">
                                                <i class="bi bi-receipt-cutoff nine"></i>
                                            </div>
                                            <p>Billing</p>
                                        </div>
                                        <i class="bi bi-circle-fill ten"></i>
                                        <div class="wrap ready eleven">
                                            <div class="checkpoint eleven">
                                                <i class="bi bi-bell eleven"></i>
                                            </div>
                                            <p>Ready for Delivery</p>
                                        </div>
                                        <i class="bi bi-circle-fill twelve"></i>
                                        <div class="wrap payment thirtheen">
                                            <div class="checkpoint thirtheen">
                                                <i class="bi bi-wallet2 thirtheen"></i>
                                            </div>
                                            <p>Payment</p>
                                        </div>
                                        <div class="wrap finish fourteen">
                                            <div class="checkpoint fourteen">
                                                <i class="bi bi-journal-check fourteen"></i>
                                            </div>
                                            <p>Finish</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="track-bottom">
                                <h5 class="mb-4" style="border-bottom: 1px solid black; padding-bottom: 8px">Detail Progress</h5>
                                
                                <div class="detail-progress">
                                    <div class="line">
                                        ${detailProgressHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-end;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            `;

                                $('#results').addClass('bottom-height');
                                $('#results').html(cardHtml).hide().fadeIn();

                                let Current = response[0].Progress;

                                // Array of elements to hide
                                const elementsToHide = [
                                    '.mobile-reception',
                                    '.mobile-service',
                                    '.mobile-inspection',
                                    '.mobile-washing',
                                    '.mobile-billing',
                                    '.mobile-ready',
                                    '.mobile-payment',
                                    '.mobile-finish'
                                ];

                                elementsToHide.forEach(element => {
                                    $(element).addClass('d-none');
                                });

                                // Mapping of progress to corresponding classes
                                const progressMapping = {
                                    'Reception': ['reception', 'inspection'],
                                    'Waiting for Service': ['reception', 'inspection'],
                                    'Service': ['service', 'inspection'],
                                    'Waiting for Inspection': ['service', 'inspection'],
                                    'Inspection': ['inspection', 'washing'],
                                    'Waiting for Washing': ['inspection', 'washing'],
                                    'Washing': ['washing', 'billing'],
                                    'Waiting for Billing': ['washing', 'billing'],
                                    'Billing': ['billing', 'notification'],
                                    'Waiting for Notification': ['billing', 'notification'],
                                    'Ready for Delivery': ['ready', 'payment'],
                                    'Waiting for Payment': ['ready', 'payment'],
                                    'Payment Finish': ['payment', 'finish'],
                                    'Finish': ['finish']
                                };

                                // Show relevant elements based on current progress
                                if (progressMapping[Current]) {
                                    const [currentClass, otherClass] = progressMapping[Current];
                                    $(`.mobile-${currentClass}`).removeClass('d-none').addClass(
                                        'check');
                                    $(`.mobile-${otherClass}`).removeClass('d-none');
                                }


                            } else {
                                $('#results').html('No data found.');
                            }
                        },
                        error: function(xhr, error, status) {
                            console.log(xhr, error, status);
                        }
                    }).done(function(response) {
                        let progress = response[0].ProgressId;

                        let classes = ['.one', '.two', '.three', '.four', '.five', '.six', '.seven',
                            '.eight', '.nine', '.ten', '.eleven', '.twelve', '.thirtheen',
                            '.fourteen'
                        ];

                        for (let i = 0; i < progress; i++) {
                            $(classes[i]).addClass('check');
                        }

                        $('#btn-track').click(function(e) {
                            $('#trackModal').modal('hide');
                        });
                    });
                }
            });
        });
    </script>
@endpush --}}
