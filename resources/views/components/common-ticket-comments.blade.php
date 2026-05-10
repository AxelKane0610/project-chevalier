<div class="ticket-comments-box">
    
    <div class="ticket-comments-body">
        <ul class="tickets-comments-items">
        {{ $slot }}
        </ul>
    </div>
    
    <div class="ticket-comments-footer">
        <form action="{{ $action1 }}" method="POST"  id="{{ $id }}" enctype="multipart/form-data">
            @csrf
            {{ $footer ?? '' }}
        </form>
        
    </div>
    
    
    
</div>