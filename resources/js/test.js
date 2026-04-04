console.log("JS LOADED");
const test = document.querySelector('.js-search-btn').addEventListener('click', alertTest);

function alertTest() {
    alert('test');
}


var softwareTicketForm = document.querySelector('#create-sw-ticket');
softwareTicketForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    fetch('/create-software-ticket', {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(new_ticket => {
        console.log(new_ticket.ticket);
        const newRow = 
        `
            <tr>
                <td>
                    <a href="/software-tickets-menu-details/{{ $ticket->id }}">
                        <i class="ti-arrow-right" ></i>
                    </a>
                </td>
                <td>${new_ticket.ticket.ticket_reciept}</td>
                <td>${new_ticket.ticket.support_type}</td>
                <td>${new_ticket.ticket.description}</td>
                <td>${new_ticket.ticket.priority}</td>
            </tr>
        
        `;
        // $('.pending-software-tickets-table tbody').append(newRow);
        document
        .querySelector('#pending-software-tickets-table tbody')
        .insertAdjacentHTML('beforeend', newRow);
    })
    .catch(error => console.error(error));

    

    alert('tạo thành công');
});