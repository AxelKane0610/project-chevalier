@props([
    'comments',
    'showAttachments' => true
])

<div class="col-lg-4">

    <div class="card shadow border-0 rounded-4 h-100 d-flex flex-column">

        <div class="card-header bg-white py-3 px-4">

            <h5 class="mb-0 fw-semibold">

                <i class="ti-comments text-primary me-2"></i>

                Comments

                <span class="badge bg-primary ms-2">

                    {{ $comments->count() }}

                </span>

            </h5>

        </div>

        <div class="card-body overflow-auto p-4">

            @forelse($comments as $comment)

                <div class="border rounded-4 p-3 mb-3 shadow-sm">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-1">
                                {{ $comment->user->fullname }}
                            </h6>
                            <small class="text-muted">
                                {{ $comment->created_at->format('Y-m-d H:i') }}
                            </small>

                        </div>

                    </div>

                    <div class="mt-3">
                        {!! nl2br(e($comment->comment)) !!}
                    </div>

                    @if($showAttachments && $comment->attachments->count())
                        <div class="mt-3">
                            <x-common-attachments-table-card
                                :attachments="$comment->attachments"/>
                        </div>
                    @endif

                </div>

            @empty
                <div class="text-center text-muted py-5">
                    <i class="ti-comments display-5 d-block mb-3"></i>
                    No comments yet.
                </div>
            @endforelse

            
        </div>

        @isset($footer)

        <div class="card-footer bg-white p-4 d-flex justify-content-center">

            {{ $footer }}

        </div>

        @endisset

        

    </div>

</div>