
<div class="d-flex justify-content-end">
    {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="hps-warehouse-items-table" class="table table-bordered table-responsive table-hover align-middle caption-top" width="100%" >
        <caption>
            
        </caption>
        <thead class="table-light text-center col-small">
            <tr>
                <th class="col-small"></th> 
                <th class="col-medium">Asset Tag</th>
                <th class="col-medium">Serial Number</th>
                <th class="col-medium">Box SN</th>
                <th class="col-medium">Product Number</th>
                <th class="col-large">Model</th>
                <th class="col-medium">Site</th>
                <th class="col-medium">Status</th>
                <th class="col-medium">Tình trạng máy</th>
            </tr>
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

    

