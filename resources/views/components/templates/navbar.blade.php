<nav>
    <a href="/admin" class="{{ $active === 'Home' ? 'nav-active' : '' }}">
        <i class="bi bi-house-door-fill"></i>
    </a>

    <a href="/admin/customer" class="{{ $active === 'Customer' ? 'nav-active' : '' }}">
        <i class="bi bi-person"></i>
    </a>

    <a href="/admin/vehicle" class="{{ $active === 'Vehicle' ? 'nav-active' : '' }}">
        <i class="bi bi-car-front"></i>
    </a>

    <a href="/settings" class="{{ $active === 'Settings' ? 'nav-active' : '' }}">
        <i class="bi bi-gear-fill"></i>
    </a>

    <a href="/logout">
        <i class="bi bi-box-arrow-right"></i>
    </a>
</nav>
