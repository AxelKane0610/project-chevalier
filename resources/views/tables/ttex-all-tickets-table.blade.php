<div class="d-flex justify-content-end sticky" >
    {{ $tickets->links('pagination::bootstrap-5') }}
</div>

<table id="all-ttex-tickets-table" class="common-table" width="100%" >
    <thead>
        <th width="5%"></th>
        <th width="10%">TTEX Bill</th>
        <th width="10%">User Owner</th>
        <th width="20%">Người gửi</th>
        <th width="20%">Người nhận</th>
        <th width="20%">Mô tả hàng hóa</th>
        <th width="10%">Status</th>

    </thead>

    <tbody>
        @foreach ($tickets as $ticket)
            
                <tr>
                    <td>
                        <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>{{ $ticket->ttex_bill }}</td>
                    <td>{{ $ticket->user_owner->fullname  }}</td>
                    <td>{{ $ticket->sender_info }}</td>
                    <td>{{ $ticket->receiver_info }}</td>
                    <td>{{ $ticket->shipment_description }}</td>
                    
                    <td>
                        <span class="ticket-status {{ $ticket->status_data['class'] }}">
                            {{ $ticket->status_data['text'] }}
                        </span>
                    </td>
                </tr>
                
        @endforeach
    </tbody>

    
</table>

    
