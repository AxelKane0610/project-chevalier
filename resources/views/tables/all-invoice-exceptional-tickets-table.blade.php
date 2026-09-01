<div class="d-flex justify-content-end sticky common-pagination" >
    {{ $all_tickets->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<table class="common-table" id="all-invoice-exceptional-tickets-table" width="100%" >
    <thead>
        <th width="5%"></th>
        <th width="14%">Receipt</th>
        <th width="20%">Support Type</th>
        <th width="39%">Issue Description</th>
        <th width="11%">Product Model</th>
        <th width="11%">Status</th>
    </thead>

    <tbody>
        @foreach ($all_tickets as $ticket)
            
            <tr>
                <td>
                    <a href="/invoice-exceptional-menu-details/{{ $ticket->id }}">
                        <button><i class="ti-arrow-right" ></i></button>
                    </a>
                </td>
                <td>{{ $ticket->ticket_receipt }}</td>
                <td>
                    
                    <span class="badge rounded-pill bg-{{ $ticket->support_type_data['color'] ?? 'primary' }} px-3 py-2">
                        {{ $ticket->support_type_data['text'] }}
                    </span>
                </td>
                <td>{{ $ticket->description }}</td>
                <td>{{ $ticket->product_model }}</td>
                <td>
                    <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                        {{ $ticket->status_data['text'] }}
                    </span>
                </td>
            </tr>
                
        @endforeach
    </tbody>
</table>