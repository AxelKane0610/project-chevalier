<div class="d-flex justify-content-end">
    {{ $tickets->links('pagination::bootstrap-5') }}
</div>

<table id="pending-loan-unit-part-tickets-table" class="common-table" width="100%" >
    <thead>
        <th width="5%"></th>
        <th width="15%">Receipt</th>
        <th width="15%">User Owner</th>
        <th width="15%">Status</th>
        <th width="15%">Customer Unit Info</th>
        

    </thead>

    <tbody>
        @foreach ($tickets as $ticket)
            <tr>
                <td>
                    
                    <a href="/loan-unit-part-ticket-details/{{ $ticket->id }}">
                        <button><i class="ti-arrow-right" ></i></button>
                    </a>
                    
                </td>
                <td>{{ $ticket->ticket_receipt }}</td>
                <td>{{ $ticket->user_owner->fullname }}</td>
                <td>
                    <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                        {{ $ticket->status_data['text'] }}
                    </span>
                </td>
                <td>{{ $ticket->customer_unit_info }}</td>

            </tr>
        @endforeach
    </tbody>
</table>