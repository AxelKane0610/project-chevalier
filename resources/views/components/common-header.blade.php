<div class="common-header-navigation">
    <h1>{{ $title }}</h1>
    <ul class="common-header-navigation-item-container">
        {{ $slot }}

        <li class="logout-btn">
            <a href="{{ url('/logout') }}" >
                <i class="ti-power-off"></i>
                Logout
            </a>
        </li>

    </ul>
</div>