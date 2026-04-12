<div class="ticket-form-overlay">
    <div class="ticket-form-box">
        <div class="ticket-form-box-content">
            <div class="ticket-form-header">
                <i class="ti-close js-close-create-software-tickets"></i> <!-- class="js-close-create-software-tickets"-->
                <h2>{{$title}}</h2>
            </div>

            <form action="{{ $action1 }}" method="POST" class="Ticket-Form-Body" id="{{ $id }}" enctype="multipart/form-data"> <!-- id="create-sw-ticket" -->
                @csrf
                {{ $slot }}

            </form>
        </div>
        
    </div>
</div>

<script>
        const TicketFormOverlay = document.querySelector(".ticket-form-overlay")
        
        const clickCreateTicket = document.querySelector('.js-create-software-tickets')
        clickCreateTicket.addEventListener('click', showTicketForm)
        const closeCreateTicket = document.querySelector('.js-close-create-software-tickets')
        closeCreateTicket.addEventListener('click', closeTicketForm)

        function showTicketForm() {
            TicketFormOverlay.classList.add('active')
        }
        function closeTicketForm() {
            TicketFormOverlay.classList.remove('active')
        }

</script>