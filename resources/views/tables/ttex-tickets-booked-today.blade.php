<div class="d-flex justify-content-end sticky common-pagination" >
    {{ $tickets_booked_today->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="ttex-tickets-booked-today-table" class="common-table" width="100%" >
        <thead>
            <tr>
                <th width="5%"></th>
                <th width="10%">Shipment Type</th>
                <th width="10%">Part Status</th>
                <th width="20%">Người gửi</th>
                <th width="20%">Người nhận</th>
                <th width="20%">Mô tả hàng hóa</th>
                <th width="15%">TTEX Bill</th>

            </tr>
        </thead>

        <tbody>
            @foreach ($tickets_booked_today as $ticket)
                
                    <tr>
                        <td>

                            <button type="button" 
                                class="btn btn-primary" 
                                onclick="window.location.href='/ttex-tickets-menu-details/{{ $ticket->id }}'">
                                <i class="ti-arrow-right"></i>
                            </button>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $ticket->shipment_type_data['color'] ?? 'primary' }} px-3 py-2">
                                {{ $ticket->shipment_type_data['text'] }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $ticket->part_status_data['color'] ?? 'primary' }} px-3 py-2">
                                {{ $ticket->part_status_data['text'] }}
                            </span>
                        </td>
                        <td>{{ $ticket->sender_info  }}</td>
                        <td>{{ $ticket->receiver_info }}</td>
                        <td>{{ $ticket->shipment_description }}</td>
                        <td>{{ $ticket->ttex_bill }}</td>
                        
                        
                    </tr>
                    
            @endforeach
        </tbody>

    </table>
</div>