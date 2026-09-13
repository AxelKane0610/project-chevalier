<!-- <div class="col-lg-4">
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

</div> -->

@php
    $threshold = 10; // Ngưỡng số dòng (ví dụ: trên 6 dòng sẽ chia 2 cột)
    $hasManyRows = count($rows) > $threshold;

    // Nếu nhiều hơn 6 dòng -> mỗi item chiếm 6/12 (2 cột). Ngược lại chiếm 12/12 (1 cột)
    $itemColClass = $hasManyRows ? 'col-md-6' : 'col-12';
    
    // Mở rộng card ngoài cùng từ col-lg-4 lên col-lg-8 khi chuyển sang 2 cột
    $cardColClass = $hasManyRows ? 'col-lg-8' : 'col-lg-4';
@endphp

<div class="col-lg-4">
    <div class="card shadow border-0 rounded-4 h-100">
        <div class="card-header bg-white py-3 px-4">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-ticket-detailed text-primary me-2"></i>
                Ticket Details
            </h5>
        </div>

        <div class="card-body p-3 d-flex flex-column flex-grow-1">

            {{-- Chia 2 cột nội bộ bằng col-6 --}}
            <div class="row gx-3 gy-2">
                @foreach($rows as $row)
                    <div class="{{ $itemColClass }}">
                        <div class="py-2 border-bottom d-flex justify-content-between align-items-center h-100">
                            
                            {{-- Tên nhãn (Label) --}}
                            <span class="text-secondary small me-2" title="{{ $row['label'] }} text-wrap text-break">
                                <i class="{{ $row['icon'] }} me-1"></i>{{ $row['label'] }}
                            </span>

                            {{-- Giá trị (Value) --}}
                            <div class="col-7 text-end fw-semibold text-break">
                                @if(($row['type'] ?? 'text') === 'badge')

                                    <span class="badge rounded-pill bg-{{ $row['color'] ?? 'primary' }} px-3 py-2 text-wrap text-start">
                                        {{ $row['value'] }}
                                    </span>

                                @else

                                    <span class="fw-semibold text-break">
                                        {{ $row['value'] }}
                                    </span>

                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{ $slot }}

            @isset($footer)
                <div class="bg-white border-top p-3 d-flex justify-content-center mt-auto">
                    {{ $footer }}
                </div>
            @endisset

        </div>
    </div>
</div>