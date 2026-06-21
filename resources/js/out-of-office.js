console.log('JS Loaded');

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-out-of-office-ticket-form') {
        e.preventDefault();
        
        fetch(url, {
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
            const newRow = 
            `
                <tr>
                    <td>
                        <a href="/out-of-office-ticket-menu-details/${new_ticket.ticket_id}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>${new_ticket.user_owner}</td>
                    <td>${new_ticket.type_of_leave}</td>
                    <td>${new_ticket.start_date}</td>
                    <td>${new_ticket.end_date}</td>
                    <td>${new_ticket.reasons_for_leave}</td>
                    <td>${new_ticket.status}</td>
                </tr>
            
            `;
            document
            .querySelector('.pending-out-of-office-tickets-table tbody')
            .insertAdjacentHTML('beforeend', newRow);
            
            document.querySelector('.ticket-form-overlay').classList.remove('active');
            e.target.reset();
        })
        .catch(error => console.error(error));

    }
});