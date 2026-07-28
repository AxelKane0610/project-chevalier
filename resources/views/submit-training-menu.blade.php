<!DOCTYPE html>
<html>
    <head>

        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite([ 'resources/js/app.js', 'resources/js/training-management.js', 'resources/css/icons/themify-icons.css'])
    
    </head>

    <body class="background-enable">

        <x-common-header title="Xin chào, hôm nay bạn cần hỗ trợ gì ?">
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
                        <form action=""  >
                            <button type="button" class="js-input-required-btn" data-target="create-training-request"><i class="ti-plus"></i> Request Training</button>
                        </form>

                    </div>

                    <div class="col-10 h-100 overflow-auto">
                        <div class="bg-white p-3 rounded shadow-sm ticket-table" id="pending-good-part-ttex-tickets-container">

                            <h2>All tickets</h2>
                            <table id="pending-ttex-tickets-table" class="common-table mh-100" width="100%" >
                                <tr>
                                    <th width="5%"></th>
                                    <th width="10%">User Owner</th>
                                    <th width="20%">Training No</th>
                                    <th width="20%">Status</th>
                                    <th width="20%">Start Date</th>
                                    <th width="20%">End Date</th>

                                </tr>
                            
                                <tbody>
                                    

                                    @foreach($all_tickets as $ticket)
                                        <tr>
                                            <td>
                                                <button type="button" 
                                                    class="btn btn-primary" 
                                                    onclick="window.location.href='/training-ticket-details/{{ $ticket->id }}'">
                                                    <i class="ti-arrow-right"></i>
                                                </button>
                                            </td>
                                            <td>{{ $ticket->user_owner->fullname }}</td>
                                            <td>{{ $ticket->training_no }}</td>
                                            <td>{{ $ticket->status }}</td>
                                            <td>{{ $ticket->start_date }}</td>
                                            <td>{{ $ticket->end_date }}</td>
                                            
                                            
                                        </tr>


                                    @endforeach
                                </tbody>

                            </table>
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