<div class="col-lg-4">
    <div class="card shadow border-0 rounded-4 h-100">
        <div class="card-header bg-white py-3 px-4">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-ticket-detailed text-primary me-2"></i>
                Ticket Details
            </h5>

        </div>

        <div class="card-body p-4 d-flex flex-column flex-grow-1">

            @foreach($rows as $row)
                <div class="row py-3 border-bottom">

                    <div class="col-4 text-secondary">
                        <i class="{{$row['icon']}} me-2"></i>{{ $row['label']}}
                    </div>

                    <div class="col-8 text-end fw-semibold">
                        @if(($row['type'] ?? 'text') === 'badge')

                            <span class="badge rounded-pill bg-{{ $row['color'] ?? 'primary' }} px-3 py-2">
                                {{ $row['value'] }}
                            </span>

                        @else

                            <span class="fw-semibold">
                                {{ $row['value'] }}
                            </span>

                        @endif
                    </div>

                </div>
            
            @endforeach

            {{ $slot }}

            @isset($footer)
                <div class="bg-white border-top p-3 d-flex justify-content-center">
                    {{ $footer }}
                </div>
            @endisset
            

        </div>

    </div>

</div>