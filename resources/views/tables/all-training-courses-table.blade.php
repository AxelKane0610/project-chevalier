<div class="d-flex justify-content-end sticky" >
    {{ $all_training_courses->links('pagination::bootstrap-5') }}
</div>


<table id="all-training-courses-table" class="common-table mh-100" width="100%" >
    <tr>
        <th width="10%">Training No</th>
        <th width="20%">Course ID</th>
        <th width="20%">Course Name</th>
        <th width="20%">Start Date</th>
        <th width="20%">End Date</th>

    </tr>

    <tbody>
        @foreach($all_training_courses as $training_course)
            <tr>
                
                <td>{{ $training_course->training_no }}</td>
                <td>{{ $training_course->course_id }}</td>
                <td>{{ $training_course->course_name }}</td>
                <td>{{ $training_course->start_date }}</td>
                <td>{{ $training_course->end_date }}</td>
                
            </tr>
        @endforeach
    </tbody>

</table>