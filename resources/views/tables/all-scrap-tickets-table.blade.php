<div class="d-flex justify-content-end">
    {{ $all_tickets->links('pagination::bootstrap-5') }}
</div>

<table id="pending-scrap-tickets-table" class="common-table" width="100%" >
    <thead>
        <th width="5%"></th>
        <th width="15%">Loại hàng hủy</th>
        <th width="15%">Người request</th>
        <th width="15%">Thông tin hàng hủy (Tóm tắt)</th>
        <th width="15%">Status</th>
        

    </thead>

    <tbody>
        @foreach ($all_tickets as $ticket)
            <tr>
                <td>
                    
                    <a href="/scrap-ticket-details/{{ $ticket->id }}">
                        <button><i class="ti-arrow-right" ></i></button>
                    </a>
                    
                </td>
                <td>{{ $ticket->scrap_type }}</td>
                <td>{{ $ticket->user_owner->fullname }}</td>
                <td>{{ $ticket->scrap_description}}</td>
                <td>
                    <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                        {{ $ticket->status_data['text'] }}
                    </span>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>