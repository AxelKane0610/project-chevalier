
<div class="d-flex justify-content-end">
    {{ $items->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="spectre-crown-warehouse-items-table" class="common-table" width="100%" >
        <thead>
            <th width="5%"></th>
            <th width="5%">Asset Tag</th>
            <th width="10%">Serial Number</th>
            <th width="10%">Box SN</th>
            <th width="10%">Product Number</th>
            <th width="20%">Model</th>
            <th width="10%">Category</th>
            <th width="10%">Warehouse</th>
            <th width="10%">Available Status</th>
            <th width="10%">Condition</th>

        </thead>

        <tbody>
            @foreach ($items as $item)
                
                <tr
                    data-category="{{ $item->category }}"
                    data-warehouse="{{ $item->warehouse }}"
                    data-availability="{{ $item->available_status }}"
                    data-condition="{{ $item->condition }}"
                >
                    <td>
                        <a href="/spectre-crown-warehouse-item-details/{{ $item->id }}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>{{ $item->asset_tag }}</td>
                    <td>{{ $item->serial_number }}</td>
                    <td>{{ $item->box_serial_number }}</td>
                    <td>{{ $item->product_number }}</td>
                    <td>{{ $item->model }}</td>
                    
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->category_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->category_data['text'] }}
                        </span>
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->warehouse_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->warehouse_data['text'] }}
                        </span>
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->available_status_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->available_status_data['text'] }}
                        </span>
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-{{ $item->condition_data['color'] ?? 'primary' }} px-3 py-2">
                            {{ $item->condition_data['text'] }}
                        </span>
                    </td>

                </tr>
            @endforeach
        </tbody>

    </table>
</div>

    

