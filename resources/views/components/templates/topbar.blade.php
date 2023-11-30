<link rel="stylesheet" href="{{ asset('assets/css/components/topbar.css') }}">

<div class="topbar">
    <i class="bi bi-list sidebar-toggle"></i>
    <div class="profile-wrap" data-bs-toggle="offcanvas" data-bs-target="#profile-drawer" aria-controls="profile-drawer">
        <div class="user-wrap">
            <span class="username">
                {{ session('user.fullname') }}
            </span>
            <span class="user-role">
                {{ $role }}
            </span>
        </div>
        <div class="avatar-wrap">
            <img src="https://th.bing.com/th/id/OIP.WJrIBdWMZQfSlBeZpgWlqQHaHa?pid=ImgDet&rs=1" alt="avatar"
                class="avatar">
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" style="width: 350px" tabindex="-1" id="profile-drawer"
    aria-labelledby="profile-drawerLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="profile-drawerLabel">Profil Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        ...
    </div>
</div>
