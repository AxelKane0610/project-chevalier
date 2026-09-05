<!DOCTYPE html>
<html>
    <head>
        <title>CENTRA</title>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="{{ asset('imgs/logo.png') }}">
        @vite([ 'resources/js/app.js', 'resources/js/training-management.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        <div>

            <x-common-header title="Training Submit Menu">
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
                            @if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TRAINING_ADMIN'))
                                <button type="button" class="js-input-required-btn" data-target="create-training-request"><i class="ti-plus"></i> Request Training</button>
                            
                            @endif

                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-pending-training-tickets-btn" data-target = "pending-training-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$pending_tickets->count()}}
                                </span>
                                <i class="ti-timer"></i> Show your pending training
                            </button>

                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-training-tickets-btn" data-target = "all-training-tickets-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$all_your_training_tickets->total()}}
                                </span>
                                <i class="ti-list-ol"></i> Show all your training tickets
                            </button>

                            <button class="btn btn-primary table-btn w-100 position-relative" id="show-all-training-courses-btn" data-target = "all-training-courses-container">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{$all_training_courses->total()}}
                                </span>
                                <i class="ti-list-ol"></i> Show All Training Courses
                            </button>
                            @if($all_country_team_training_tickets->total() > 0)
                                <button class="btn btn-primary table-btn w-100 position-relative" id="show-team-country-training-tickets-btn" data-target = "all-country-team-training-tickets-container">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{$all_country_team_training_tickets->total()}}
                                    </span>
                                    <i class="ti-list-ol"></i> Check your teams/country training status
                                </button>
                            @endif

                        </div>

                        <div class="col-10 h-100 overflow-auto">
                            <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-training-tickets-container">
                                

                                <h2>Pending Training</h2>
                                <table id="pending-training-tickets-table" class="common-table mh-100" width="100%" >

                                    <tr>
                                        <th width="5%"></th>
                                        <th width="10%">User Owner</th>
                                        <th width="20%">Training No</th>
                                        <th width="20%">Status</th>
                                        <th width="20%">Start Date</th>
                                        <th width="20%">End Date</th>

                                    </tr>
                                
                                    <tbody>
                                        

                                        @foreach($pending_tickets as $ticket)
                                            <tr>
                                                <td>

                                                    <a href="/training-ticket-details/{{ $ticket->id }}">
                                                        <button><i class="ti-arrow-right" ></i></button>
                                                    </a>
                                                </td>
                                                <td>{{ $ticket->user_owner->fullname }}</td>
                                                <td>{{ $ticket->training_no }}</td>
                                                <td>
                                                    <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                                        {{ $ticket->status_data['text'] }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->start_date }}</td>
                                                <td>{{ $ticket->end_date }}</td>
                                                
                                                
                                            </tr>


                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-training-tickets-container">
                                <div class="common-table-filter">
                                    <h2>All your trainings</h2>
                                    <div class="filter-group">
                                        <h2>Training No</h2>
                                        <select class="ajax-filter" name="training_no" id="all-training-tickets-training-no-filter">
                                            <option value="">All</option>
                                            @foreach($all_training_no_numbers as $training_no_numbers)
                                                <option value="{{ $training_no_numbers }}">{{ $training_no_numbers }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <h2>Status</h2>
                                        <select class="ajax-filter" name="status" id="all-training-tickets-status-filter">

                                            <option value="">All</option>
                                            <option value="1">Open</option>
                                            <option value="2">Chưa submit</option>
                                            <option value="3">Đã submit, chờ verify</option>
                                            <option value="4">Completed</option>
                                            <option value="5">Rejected</option>
                                            
                                        </select>
                                    </div>

                                    
                                </div>

                                <div id="all-your-training-tickets-table-container">
                                    @include('tables.all-individual-training-tickets')
                                </div>

                            </div>

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none ajax-table" id="all-training-courses-container">
                                <div class="common-table-filter">
                                    <div class="filter-group">
                                        <h2>All Training Courses</h2>
                                        <div class="search-box">
                                            <i class="ti-search"></i>
                                            <input class="ajax-search" type="text" placeholder="Search course id, course name" id="search-all-training-course-input">
                                        </div>
                                    </div>

                                    <div class="filter-group">
                                        <h2>Training No</h2>
                                        <select class="ajax-filter" name="training_no" id="all-training-courses-training-no-filter">
                                            <option value="">All</option>
                                            @foreach($all_training_no_numbers as $training_no_numbers)
                                                <option value="{{ $training_no_numbers }}">{{ $training_no_numbers }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>

                                    


                                </div>

                                <div id="all-training-courses-table-container">
                                    @include('tables.all-training-courses-table')
                                </div>
                            </div>

                            <div class="bg-white p-3 rounded shadow-sm ticket-table d-none" id="all-country-team-training-tickets-container">
                                <div class="common-table-filter">
                                    <div class="filter-group">
                                        <h2>Your Team/Country training status</h2>
                                        <div class="search-box">
                                            <i class="ti-search"></i>
                                            <input class="ajax-search" type="text" placeholder="Search tên user, training no" id="search-all-training-tickets-input">
                                        </div>
                                    </div>

                                    <div class="filter-group">
                                        <h2>Status:</h2>
                                        <select id="all-training-tickets-status-filter" name="status" class="ajax-filter">
                                            <option value="">All</option>
                                            <option value="1">Open</option>
                                            <option value="2">Chưa submit</option>
                                            <option value="3">Đã submit, chờ verify</option>
                                            <option value="4">Completed</option>
                                            <option value="5">Rejected</option>
                                            
                                        </select>
                                    </div>

                                    <div class="filter-group">
                                        <h2>Training No</h2>
                                        <select id="all-training-tickets-training-no-filter" name="training_no" class="ajax-filter">
                                            <option value="">All</option>
                                            @foreach($all_training_no_numbers as $training_no_numbers)
                                                <option value="{{ $training_no_numbers }}">{{ $training_no_numbers }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>


                                </div>
                                
                                <div id="all-country-team-training-tickets-table-container">
                                    @include('tables.all-country-team-training-tickets-table')
                                </div>
                                
                            </div>

                        </div>

                            
                            

                        
                    </div>
                </div>
            </div>


        <x-common-ticket-form title="Request Training" action1="/request-training" id="create-training-request">
            <table class="table" id="course-table">
                <thead>
                    <tr>
                        <th width="30%">Course ID</th>
                        <th width="60%">Course Name</th>
                        <th width="10%"></th>
                    </tr>
                </thead>

                <tbody id="course-body">
                    <tr>
                        <td>
                            <input type="text"
                                name="course_id[]"
                                class="form-control"
                                required>
                        </td>

                        <td>
                            <input type="text"
                                name="course_name[]"
                                class="form-control"
                                required>
                        </td>

                        <td>
                            <button type="button" class="btn btn-danger remove-row">
                                X
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-primary" id="add-row">
                + Add Row
            </button>

            <lable>Start Date</label>
            <input type="date" class="ticket-form-body-input" name="start_date" required>

            <lable>End Date</label>
            <input type="date" class="ticket-form-body-input" name="end_date" required>

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit">Request</button> 
            </x-slot:footer>

        </x-common-ticket-form>
    </body>

</html>