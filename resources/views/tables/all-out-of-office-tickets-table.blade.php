<div class="d-flex justify-content-end sticky common-pagination" >
    {{ $all_tickets->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<table class="common-table pending-out-of-office-tickets-table" width="100%" >
    <thead>
        <tr>
            <th width="5%"></th>
            <th width="10%">User Owner</th>
            <th width="10%">Type of Leave</th>
            <th width="10%">Start Date</th>
            <th width="10%">End Date</th>
            <th width="25%">Reasons for leave</th>
            <th width="10%">Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($all_tickets as $ticket)
            
            <tr>
                <td>
                    <a href="/out-of-office-tickets-menu-details/{{ $ticket->id }}">
                        <button><i class="ti-arrow-right" ></i></button>
                    </a>
                </td>
                <td>{{ $ticket->user_owner->fullname }}</td>
                <td>
                    <span class="badge rounded-pill bg-{{ $ticket->type_of_leave_data['color'] ?? 'primary' }} px-3 py-2">
                        {{ $ticket->type_of_leave_data['text'] }}
                    </span>
                </td>
                <td>{{ $ticket->start_date }}</td>
                <td>{{ $ticket->end_date }}</td>
                <td>{{ $ticket->reasons_for_leave }}</td>
                <td>
                    <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                        {{ $ticket->status_data['text'] }}
                    </span>
                </td>
            </tr>
                
        @endforeach
    </tbody>
</table>