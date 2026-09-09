
<div class="d-flex justify-content-end">
    {{ $items->links('pagination::bootstrap-5') }}
</div>

<div class="common-table-container">
    <table id="hps-warehouse-items-table" class="common-table" width="100%" >
        <thead>
            <th width="5%"></th>
            <th width="5%">Asset Tag</th>
            <th width="10%">Serial Number</th>
            <th width="10%">Box SN</th>
            <th width="10%">Product Number</th>
            <th width="20%">Model</th>
            <th width="10%">Site</th>
            <th width="10%">Status</th>
            <th width="10%">Tình trạng máy</th>

        </thead>

        <tbody>
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
                    <td>{{ $item->current_site }}</td>
                    <td>{{ $item->current_status }}</td>
                    <td>{{ $item->unit_re_import_status }}</td>
                    

                </tr>
            @endforeach
        </tbody>

    </table>
</div>

    

