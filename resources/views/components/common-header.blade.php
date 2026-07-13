<div class="common-header-navigation">
    <h1>{{ $title }}</h1>
    <ul class="common-header-navigation-item-container">
        {{ $slot }}
        
        
        <div class="user-account-menu">
            <button>
                <i class="ti-user"></i>
            </button>
                
            <div class="user-dropdown">

                <form action="{{ route('user-profile') }}" method="GET">
                    <button type="submit">Profile</button>
                </form>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Logout</button>
                </form>

            </div>

        </div>

    </ul>
</div>