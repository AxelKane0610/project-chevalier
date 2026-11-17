
<div class="d-flex justify-content-end">
    {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="hps-warehouse-items-table" class="table table-bordered table-responsive table-hover align-middle" width="100%" >
        <thead class="table-light text-center col-small">
            <th class="col-small"></th>
            <th class="col-small">Asset Tag</th>
            <th class="col-small">Serial Number</th>
            <th class="col-small">Box SN</th>
            <th class="col-small">Product Number</th>
            <th class="col-small">Model</th>
            <th class="col-small">Site</th>
            <th class="col-small">Status</th>
            <th class="col-small">Tình trạng máy</th>

        </thead >

        <tbody class="text-center">
            @foreach ($items as $item)
                
                <tr>
                    <td>
                        <a href="/hps-warehouse-item-details/{{ $item->id }}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>{{ $item->asset_tag }}</td>
                    <td>{{ $item->current_serial_number }}</td>
                    <td>{{ $item->current_box_serial_number }}</td>
                    <td>{{ $item->current_product_number }}</td>
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
                    
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->unit_re_import_status_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->unit_re_import_status_data['text'] }}
                        </span>
                    </td>
                    

                </tr>
            @endforeach
        </tbody>

    </table>
</div>

    

