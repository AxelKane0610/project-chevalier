<div class="d-flex justify-content-end sticky common-pagination" >
    {{ $tickets->withQueryString()->links('pagination::bootstrap-5') }}
</div>


<div class="common-table-container" style="overflow-y: scroll; height: 400px;">
    <table id="all-ttex-tickets-table" class="common-table " width="100%" >
        <thead>
            <tr>
                <th width="5%"></th>
                <th width="10%">TTEX Bill</th>
                <th width="10%">User Owner</th>
                <th width="20%">Người gửi</th>
                <th width="20%">Người nhận</th>
                <th width="20%">Mô tả hàng hóa</th>
                <th width="10%">Status</th>
            </tr>

        </thead>

        <tbody >
            @foreach ($tickets as $ticket)
                
                    <tr>
                        <td>
                            <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                                <button><i class="ti-arrow-right" ></i></button>
                            </a>
                        </td>
                        <td>{{ $ticket->ttex_bill }}</td>
                        <td>{{ $ticket->user_owner->fullname ?? 'N/A' }}</td>
                        <td>{{ $ticket->sender_info }}</td>
                        <td>{{ $ticket->receiver_info }}</td>
                        <td>{{ $ticket->shipment_description }}</td>
                        
                        <td>
                            <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                                {{ $ticket->status_data['text'] }}
                            </span>
                        </td>
                    </tr>
                    
            @endforeach
        </tbody>

        
    </table>

</div>

    
