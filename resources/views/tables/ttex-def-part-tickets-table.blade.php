
<div class="d-flex justify-content-end sticky common-pagination">
    {{ $tickets_def_part_pending->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="pending-ttex-tickets-table" class="common-table mh-100" width="100%" >
        <thead>
            <tr>
                <th width="5%"></th>
                <th width="10%">Shipment Type</th>
                <th width="20%">Người gửi</th>
                <th width="20%">Người nhận</th>
                <th width="20%">Mô tả hàng hóa</th>
                <th width="15%">Note</th>
                <th width="10%">Hạn trả def cho kho</th>
                <th width="10%">Status</th>

            </tr>
        </thead>

        <tbody>
            

            @foreach($tickets_def_part_pending as $date => $group)

                <tr class="table-secondary">
                    <td colspan="10">
                        ▼ Hạn trả def cho kho:
                        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                        ({{ count($group) }})
                    </td>
                </tr>

                @foreach($group as $ticket)

                <tr>
                    <td >
                        <div class="d-flex justify-content-center align-items-center gap-2 flex-row">
                            <a href="/ttex-tickets-menu-details/{{ $ticket->id }}">
                                <button><i class="ti-arrow-right" ></i></button>
                            </a>
                            @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')))

                                <!-- <input type="checkbox" name="booking_def[]" value="{{ $ticket->id }}" form="booking-def-part"> -->
                                <input type="checkbox" 
                                        name="booking_def[]" 
                                        value="{{ $ticket->id }}" 
                                        class="booking-def-checkbox" 
                                        form="booking-def-part">
                            @endif
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-{{ $ticket->shipment_type_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $ticket->shipment_type_data['text'] }}
                        </span>
                    </td>
                    <td>{{ $ticket->sender_info  }}</td>
                    <td>{{ $ticket->receiver_info }}</td>
                    <td>{{ $ticket->shipment_description }}</td>
                    <td>{{ $ticket->note }}</td>
                    <td>{{ $ticket->part_return_deadline }}</td>
                    
                    <td>
                        <span class="badge rounded-pill bg-{{ $ticket->status_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $ticket->status_data['text'] }}
                        </span>
                    </td>
                </tr>

                @endforeach

            @endforeach
        </tbody>

    </table>
</div>