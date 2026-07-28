<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite(['resources/js/app.js', 'resources/js/thermal-event.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body class="background-enable">
        <x-common-header title="Training Ticket Details">
            <li>
                <form action="/submit-training-menu">
                    <button type="submit"><i class="ti-home"></i>Home</button>
                </form>
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

        <div class="common-table-container">
            <table class="common-table" width="100%">
                <thead>
                    <th width="25%">Training No</th>
                    <th width="25%">Course ID</th>   
                    <th width="25%">Course Name</th>
                    <th width="25%">Start Date</th> 
                    <th width="25%">End Date</th> 
                </thead>
                
                @foreach($ticket_details->training_courses as $training_course)
                <tbody>
                    
                    <tr>
                        
                        <td>{{$training_course->training_no}}</td>
                        <td>{{$training_course->course_id}}</td>
                        <td>{{$training_course->course_name}}</td>
                        <td>{{$training_course->start_date}}</td>
                        <td>{{$training_course->end_date}}</td>

                    </tr>
                
                </tbody>
                @endforeach
                
            </table>
        </div>

    </body>

</html>