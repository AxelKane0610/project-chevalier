<div class="d-flex justify-content-end sticky" >
    {{ $all_country_team_training_tickets->links('pagination::bootstrap-5') }}
</div>

<table id="your-team-country-tickets-table" class="common-table mh-100" width="100%" >

    <tr>
        <th width="5%"></th>
        <th width="10%">User Owner</th>
        <th width="20%">Training No</th>
        <th width="20%">Status</th>
        <th width="20%">Start Date</th>
        <th width="20%">End Date</th>

    </tr>

    <tbody>
        

        @foreach($all_country_team_training_tickets as $ticket)
            <tr>
                <td>
                    <a href= "/training-ticket-details/{{ $ticket->id }}" class="button">
                        <button><i class="ti-arrow-right"></i>
                        </button>
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