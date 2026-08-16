<div class="common-header-navigation vh-10">
    <h1>{{ $title }}</h1>
    <ul class="common-header-navigation-item-container">
        {{ $slot }}
        
        
        <div class="user-account-menu">
            <button>
                <i class="ti-user"></i>
            </button>
                
            <div class="user-dropdown">

                <!-- <form action="{{ route('user-profile', auth()->user()->id) }}" method="GET">
                    <button type="submit">Profile</button>
                </form> -->
                <a href="{{ route('user-profile', auth()->user()->id) }}" class="button">
                    <button><i class="ti-user"></i>
                    Profile
                    </button>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"><i class="ti-power-off"></i>Logout</button>
                </form>

            </div>

        </div>

    </ul>
</div>