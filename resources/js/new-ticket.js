console.log("JS LOADED");
const test = document.querySelector('.js-search-btn').addEventListener('click', alertTest);

function alertTest() {
    Swal.fire({
            title: 'Success!',
            text: 'Ticket created successfully.',
            input: 'text',
            icon: 'success',
            confirmButtonText: 'OK',
            scrollbarPadding: false
        });
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
        console.log(new_ticket);
        Swal.fire({
            title: 'Success!',
            text: 'Ticket created successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        });
        const newRow = 
        `
            <tr>
                <td>
                    <a href="/software-tickets-menu-details/${new_ticket.ticket_id}">
                        <i class="ti-arrow-right" ></i>
                    </a>
                </td>
                <td>${new_ticket.ticket_reciept}</td>
                <td>${new_ticket.support_type}</td>
                <td>${new_ticket.description}</td>
                <td>${new_ticket.priority}</td>
            </tr>
        
        `;
        document
        .querySelector('#pending-software-tickets-table tbody')
        .insertAdjacentHTML('beforeend', newRow);
    })
    .catch(error => console.error(error));
    closeTicketForm();
    softwareTicketForm.reset();
    

    alert('tạo thành công');
});