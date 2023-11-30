<link rel="stylesheet" href={{ asset('assets/css/components/sidebar.css') }}>

<div class="sidebar">
    <div class="logo-wrap center-row">
        <img class="logo" src={{ asset('assets/img/logo.png') }}></img>
        <span class="title">Service Tracking</span>
    </div>
    <div class="sidebar-content">
        <p class="gap">
            Menu Utama
        </p>

        <a href="/admin" class="btn sidebar-btn {{ $active === 'Home' ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            Beranda
        </a>

        @if ($role === 'Service Advisor' || $role === 'Admin')
            <p class="gap">
                Data
            </p>

            <a href="/admin/customer" class="btn sidebar-btn {{ $active === 'Customer' ? 'active' : '' }}">
                <i class="bi bi-person"></i>
                Pelanggan
            </a>

            <a href="/admin/vehicle" class="btn sidebar-btn {{ $active === 'Vehicle' ? 'active' : '' }}">
                <i class="bi bi-car-front"></i>
                Kendaraan
            </a>
        @endif

        <p class="gap">
            Lainnya
        </p>
        <a href="/logout" class="btn sidebar-btn">
            <i class="bi bi-box-arrow-right"></i>
            Keluar
        </a>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            $('.sidebar-toggle').click(function(e) {
                e.preventDefault();

                $('.sidebar').toggleClass('close');
                $('.sidebar-toggle').toggleClass('toggle-closed');
                $('.content').toggleClass('sidebar-closed');
            });
        });
    </script>
@endpush
