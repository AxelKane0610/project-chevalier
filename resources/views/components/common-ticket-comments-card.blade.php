@props([
    'comments',
    'showAttachments' => true,
    'actionRoute' => null
])

<div class="col-lg-4" >

    <div class="card shadow border-0 rounded-4 h-100 ">

        <div class="card-header bg-white py-3 px-4">

            <h5 class="mb-0 fw-semibold">

                <i class="ti-comments text-primary me-2"></i>

                Comments

                <span class="badge bg-primary ms-2">

                    {{ $comments->count() }}

                </span>

            </h5>

        </div>

        <div class="card-body overflow-y-scroll p-4 h-70" style="max-height: 70%;">

            @forelse($comments as $comment)

                <div class="border rounded-4 p-3 mb-3 shadow-sm ">
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

        

        <div class="card-footer bg-white p-4 d-flex flex-column w-100">
            <label>Write a comment</label>
            <form action="{{ $actionRoute }}" class="w-100" method="POST" enctype="multipart/form-data">
                @csrf
                <textarea name="comment" style="height: 100px; font-family: inherit ;" placeholder="Nhập comment tại đây" class="w-100"></textarea>
                <label class="ticket-form-body-input">Attach File:</label>
                <div class="upload-group ">
                    <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                    <ul class="file-list"></ul>
                </div>
                <button type="submit"><i class="ti-comment"></i>Comment</button>
            </form>

        </div>

        

        

    </div>

</div>