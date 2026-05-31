console.log("JS LOADEDD");

document.addEventListener('submit', function (e) {
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    // Kiểm tra xem form nào đang được submit dựa vào ID
    if (e.target && e.target.id === 'create-laser-engraving-ticket-form') {
        e.preventDefault();
        const buttons = this.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);
        const formData = new FormData(e.target);
        fetch('/create-laser-engraving-ticket', {
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
                        <a href="/laser-engraving-tickets-menu-details/${new_ticket.ticket_id}">
                            <button><i class="ti-arrow-right" ></i></button>
                        </a>
                    </td>
                    <td>${new_ticket.ticket_reciept}</td>
                    <td>${new_ticket.support_type}</td>
                    <td>${new_ticket.description}</td>
                    <td>${new_ticket.priority}</td>
                </tr>
            
            `;
            document
            .querySelector('#pending-laser-engraving-tickets-table tbody')
            .insertAdjacentHTML('beforeend', newRow);
            
            document.querySelector('.ticket-form-overlay').classList.remove('active');
            e.target.reset();
        })
        .catch(error => console.error(error));

    }

    if (e.target && e.target.id === 'change-laser-engraving-status-to-in-progress') {
        e.preventDefault();
        Swal.fire({
            title: 'Chuyển ticket sang In Progress ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PATCH',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire(
                        'Success!',
                        'Ticket status updated successfully.',
                        'success'
                    );
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

    if (e.target && e.target.id === 'close-laser-engraving-ticket-form') {
        e.preventDefault();
        const form = e.target;
        Swal.fire({
            title: 'Bạn có chắc muốn close ticket này ?',
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
});