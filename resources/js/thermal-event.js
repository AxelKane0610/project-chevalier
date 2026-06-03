import Swal from "sweetalert2";

console.log("JS LOADEDD");
document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');
    
    if (e.target && e.target.id === 'create-thermal-event-ticket-form') {
        e.preventDefault();
        const buttons = this.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);
        const formData = new FormData(e.target);
        fetch('/create-thermal-event-ticket', {
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
                    <td>${new_ticket.priority}</td>
                </tr>
            
            `;
            document
            .querySelector('#pending-thermal-event-tickets-table tbody')
            .insertAdjacentHTML('beforeend', newRow);
            
            document.querySelector('.ticket-form-overlay').classList.remove('active');
            e.target.reset();
        })
        .catch(error => console.error(error));

    }

    if (e.target && e.target.id === 'edit-ticket-details')
    {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn edit ticket này ?',
            icon: 'warning',
            showCancelButton: true
        })
        .then((result) => {

            // Cancel
            if (!result.isConfirmed) {
                return;
            }

            // Confirm mới loading
            startButtonLoading(form);

            fetch(url,{
                method:'POST',
                body:formData,
                headers:{
                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'input[name="_token"]'
                        ).value
                }
            })
            .then(response => response.json())
            .then(data => 
            {
                if (data.success === true) {
                    Swal.fire({
                        title:'Success',
                        text:data.message,
                        icon:'success'
                    }).then(()=>{
                    location.reload();
                });
                }
                else {
                    Swal.fire({
                        title:'Error',
                        text:data.message,
                        icon:'error'
                    }).then(()=>{
                    location.reload();
                });
                }

            })
            .catch(error => {

                Swal.fire({
                    title:'Error',
                    text:error,
                    icon:'error'
                });

            })
            .finally(() => {

                stopButtonLoading(form);

            });

        });
    }

    if (e.target && e.target.id === 'approve-ticket-form')
    {
        e.preventDefault();
        const form = e.target;
        form.dataset.loading = "true";
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to approve this ticket.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with the approval logic
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(ticket_approval_response => {
                    form.dataset.loading = "false";
                    if (ticket_approval_response.success == true) {
                        Swal.fire({
                            title: 'Success!',
                            text: ticket_approval_response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            document.querySelector('.ticket-form-overlay').classList.remove('active');
                            e.target.reset();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: ticket_approval_response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then(()=>{
                            location.reload();
                        });
                    }
                    
                })
                .catch(error => {
                    form.dataset.loading = "false";
                    Swal.fire({
                        title: 'Error!',
                        text: error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    })
                    console.error(error)
                });

            }
        });
    
    }

    if (e.target && e.target.id === 'reject-ticket-form')
    {
        e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to reject this ticket.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => 
                {
                    if (result.isConfirmed) {
                        // Proceed with the rejection logic
                        fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(response => response.json())
                        .then(ticket_approval_response => {
                            if (ticket_approval_response.success == true) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: ticket_approval_response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    document.querySelector('.ticket-form-overlay').classList.remove('active');
                                    e.target.reset();
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: ticket_approval_response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                }).then(()=>{
                                    location.reload();
                                });
                            }
                            
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            })
                            console.error(error)
                        });

                    }
                });
    }
});