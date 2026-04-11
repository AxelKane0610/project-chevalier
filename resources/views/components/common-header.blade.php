<div class="common-header-navigation">
    <h1>{{ $title }}</h1>
    <ul class="common-header-navigation-item-container">
        {{ $slot }}

        <li class="logout-btn">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">
                    <i class="ti-power-off"></i>
                        Logout
                </button>
            </form>
        
        </li>

    </ul>
</div>