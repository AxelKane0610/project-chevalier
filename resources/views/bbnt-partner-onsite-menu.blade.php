<!DOCTYPE html>
<html>
    <head>

        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite(['resources/js/app.js', 'resources/js/bbnt.js',  'resources/css/icons/themify-icons.css', ])
    </head>

    <body class="background-enable">

        <x-common-header title="Quản lý biên bản nghiệm thu">
            <li>
                <a href="{{ url('/main-menu') }}" class="button">
                    <button><i class="ti-home"></i>
                    Home
                    </button>
                </a>
            </li>
            <li>
                <div class="search-container">
                    <form action="">
                        <button type="button" id="btn-toggle-search" class="nav-btn search-btn">
                            <i class="ti-search"></i> Search
                        </button>

                        <div id="search-dropdown" class="search-dropdown-box hidden">
                            <div class="search-input-group">
                                
                                @livewire('quick-search-dropdown')
                            </div>
                        </div>
                    </form>
                </div>
            </li>
        </x-common-header>


        <div class="d-flex flex-grow-1 overflow-hidden vh-100">
            <div class="container-fluid my-5 flex-grow-1">
                <div class="row flex-grow-1 h-100">
                    <div class="col-2 d-flex justify-content-center align-items-center flex-column gap-3">

                        
                        <button type="button" class="js-input-required-btn" data-target="create-bbnt-ticket-form"><i class="ti-plus"></i> Create Ticket</button>
                        
                        
                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-items-btn" data-target = "pending-bbnt-ticket-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$pending_tickets->total()}}
                            </span>
                            <i class="ti-list"></i> 
                            Pending Tickets
                        </button>


                        <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-items-btn" data-target = "all-bbnt-ticket-container">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{$all_tickets->total()}}
                            </span>
                            <i class="ti-list"></i> 
                            Show All Tickets
                        </button>

                        


                    </div>
                </div>
            </div>
        </div>


        <x-common-ticket-form title="Request nghiệm thu" id="create-bbnt-ticket-form" action1="/import-asset">
            @method('POST')



        </x-common-ticket-form>



    </body>
</html>