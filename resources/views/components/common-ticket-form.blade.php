<div class="ticket-form-overlay" id-form="{{ $id }}"> 
    <div class="ticket-form-box">
        <div class="ticket-form-box-content">
            <div class="ticket-form-header">
                <i class="ti-close js-close-input-form"></i> <!-- class="js-close-create-software-tickets"-->
                <h2>{{$title}}</h2>
            </div>

            <form action="{{ $action1 }}" method="POST" class="Ticket-Form-Body" id="{{ $id }}" enctype="multipart/form-data"> <!-- id="create-sw-ticket" -->
                @csrf
                {{ $slot }}

            </form>
        </div>
        
    </div>
</div>


