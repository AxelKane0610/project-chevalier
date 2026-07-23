<div class="col-lg-4">
    <div class="card shadow border-0 rounded-4 h-100">
        <div class="card-header bg-white py-3 px-4">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-ticket-detailed text-primary me-2"></i>
                Ticket Tracking info
            </h5>

        </div>


        <div class="card-body p-4">

            <div class="tracking-timeline">

                @foreach($trackings as $tracking)

                    <div class="timeline-item">

                        <div class="timeline-circle {{ $loop->last ? 'completed' : '' }}"></div>

                        <div class="timeline-content">

                            <div class="fw-semibold">
                                {{ $tracking->user->fullname }}
                            </div>

                            <div class="text-secondary">
                                {{ ucfirst($tracking->action) }}
                            </div>

                            <div class="small text-muted mt-1">
                                <i class="ti-time me-1"></i>
                                {{ $tracking->created_at->format('Y-m-d H:i:s') }}
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>
    </div>

</div>