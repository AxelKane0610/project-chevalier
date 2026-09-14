
<div class="d-flex justify-content-end">
    {{ $pending_items->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="hps-warehouse-items-table" class="common-table" width="100%" >
        <thead>
            <th width="5%"></th>
            <th width="10%">HPS Receipt</th>
            <th width="10%">Serial Number</th>
            
            <th width="20%">Model</th>
            <th width="10%">Site</th>
            <th width="10%">Status</th>

        </thead>

        <tbody>
            @foreach ($pending_items as $item)
                
                <tr>
                    <td>
                        <a href="/hps-warehouse-item-details/{{ $item->id }}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>{{ $item->current_hps_receipt }}</td>
                    <td>{{ $item->current_serial_number }}</td>
                    
                    <td>{{ $item->model }}</td>
                    
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->current_site_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->current_site_data['text'] }}
                        </span>
                    </td>
                    
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->current_status_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->current_status_data['text'] }}
                        </span>
                    </td>
                    
                    

                </tr>
            @endforeach
        </tbody>

    </table>
</div>

    

