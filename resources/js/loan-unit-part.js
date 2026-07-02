console.log("JS LOADEDD");
document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-loan-unit-part-ticket') {
        e.preventDefault();
        
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn tạo ticket này ?',
            icon: 'warning',
            showCancelButton: true
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            startButtonLoading(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(new_ticket => {
                if (new_ticket.success === true) {
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
                            <a href="/loan-unit-part-ticket-details/${new_ticket.ticket_id}">
                                <button><i class="ti-arrow-right" ></i></button>
                            </a>
                        </td>
                        <td>${new_ticket.ticket_receipt}</td>
                        <td>${new_ticket.status}</td>
                        <td>${new_ticket.customer_unit_info}</td>
                    </tr>
                
                `;
                document
                .querySelector('#pending-loan-unit-part-tickets-table tbody')
                .insertAdjacentHTML('beforeend', newRow);
                stopButtonLoading(form);
                document.querySelector('.ticket-form-overlay').classList.remove('active');
                e.target.reset();

            } else {
                Swal.fire({
                    title:'Error',
                    text:new_ticket.message,
                    icon:'error'
                });
                stopButtonLoading(form);
            }
        })
        .catch(error => console.error(error));
        })


    }

    if (e.target && e.target.id === 'edit-ticket-details') {
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
                    });
                    stopButtonLoading(form);
                }

            })
            .catch(error => console.error(error));

        });
    }

});


document.querySelectorAll('.btn-edit-part').forEach(button => {

    button.addEventListener('click', function () {
        document.getElementById('edit-receipt-part-details').value =
            this.dataset.receipt;

        document.getElementById('edit-part-number').value =
            this.dataset.number;

        document.getElementById('edit-part-description').value =
            this.dataset.description;

        document.getElementById('edit-part-ct-number').value =
            this.dataset.ct;

        document.getElementById('edit-thermal-event-part-details').action =
            '/edit-thermal-event-part-details/' + this.dataset.id;

        
        // console.log(document.getElementById('edit-thermal-event-part-details').action);

    });

});