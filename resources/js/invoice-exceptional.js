console.log("JS LOADEDD");
document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-invoice-exceptional-ticket-form') {
        e.preventDefault();
        const buttons = this.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);
        const formData = new FormData(e.target);
        fetch('/create-invoice-exceptional-ticket', {
            method: 'POST',
            body: formData,
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
            buttons.forEach(btn => btn.disabled = false);
            const newRow = 
            `
                <tr>
                    <td>
                        <a href="/software-tickets-menu-details/${new_ticket.ticket_id}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>${new_ticket.ticket_receipt}</td>
                    <td>${new_ticket.support_type}</td>
                    <td>${new_ticket.description}</td>
                    <td>${new_ticket.product_model}</td>
                    <td>${new_ticket.status}</td>
                </tr>
            
            `;
            document
            .querySelector('#pending-invoice-exceptional-tickets-table tbody')
            .insertAdjacentHTML('beforeend', newRow);
            
            document.querySelector('.ticket-form-overlay').classList.remove('active');
            e.target.reset();
        })
        .catch(error => console.error(error));

    }
});