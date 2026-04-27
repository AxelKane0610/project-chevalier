<div class="tickets-details-box">
    
    <div class="ticket-details-body">
        <ul class="tickets-details-items">
        {{ $slot }}
        </ul>
    </div>
    
    <div class="ticket-details-footer">
        <form action="">
            {{ $footer ?? '' }}
        </form>
        
    </div>
    
    
    
</div>