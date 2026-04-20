<div class="ticket-form-overlay" id-form="{{ $id }}"> 
    <div class="ticket-form-box">
        <div class="ticket-form-box-content">
            <div class="ticket-form-header">
                <i class="ti-close js-close-input-form"></i> <!-- class="js-close-create-software-tickets"-->
                <h2>{{$title}}</h2>
            </div>

            
                <form action="{{ $action1 }}" method="POST"  id="{{ $id }}" enctype="multipart/form-data"> <!-- id="create-sw-ticket" -->
                    <div class="ticket-form-body">
                        @csrf
                        {{ $slot }}
                    </div>

                    <div class="ticket-form-footer">
                        {{ $footer ?? '' }}
                    </div>
                    
                </form>
            

            
            
        </div>
        
    </div>
</div>


