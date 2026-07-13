import Swal from "sweetalert2";

console.log("JS LOADEDD");


document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');
    
    if (e.target && e.target.id === 'create-thermal-event-ticket-form') {
        e.preventDefault();

        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn tạo ticket này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            startButtonLoading(form);

            fetch('/create-thermal-event-ticket', {
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
                        confirmButtonText: 'OK',
                        heightAuto: false
                    });
                    if (checkMultipartAffected.value === '1') {
                        const newRow = 
                        `
                            <tr>
                                <td>
                                    <a href="/thermal-event-tickets-menu-details/${new_ticket.ticket_id}">
                                        <button><i class="ti-arrow-right" ></i></button>
                                    </a>
                                </td>
                                <td>${new_ticket.ticket_receipt}</td>
                                <td>${new_ticket.user_owner}</td>
                                <td>${new_ticket.description}</td>
                                <td>${new_ticket.status}</td>
                            </tr>
                        
                        `;
                        document
                        .querySelector('#pending-thermal-event-tickets-table tbody')
                        .insertAdjacentHTML('beforeend', newRow);
                        
                        document.querySelector('.ticket-form-overlay').classList.remove('active');
                        e.target.reset();
                    } else {
                        window.location.href = `/thermal-event-tickets-menu-details/${new_ticket.ticket_id}`;
                    }
                } else {
                Swal.fire({
                    title:'Error',
                    text:new_ticket.message,
                    icon:'error',
                    heightAuto: false
                });
                stopButtonLoading(form);
            }
        })
        .catch(error => console.error(error));
        })

    }

    if (e.target && e.target.id === 'edit-ticket-details')
    {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn edit ticket này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
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
                        icon:'success',
                        heightAuto: false
                    }).then(()=>{
                    location.reload();
                });
                }
                else {
                    Swal.fire({
                        title:'Error',
                        text:data.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }

            })
            .catch(error => console.error(error));

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
            confirmButtonText: 'Yes, approve it!',
            heightAuto: false
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
                            confirmButtonText: 'OK',
                            heightAuto: false
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
                            confirmButtonText: 'OK',
                            heightAuto: false
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
                        confirmButtonText: 'OK',
                        heightAuto: false
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
                confirmButtonText: 'Yes, reject it!',
                heightAuto: false
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
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                confirmButtonText: 'OK',
                                heightAuto: false
                            })
                            console.error(error)
                        });

                    }
                });
    }

    if (e.target && e.target.id === 're-open-ticket-form')
    {
        e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to re-open this ticket.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, re-open it!',
                heightAuto: false
            }).then((result) => 
                {
                    if (result.isConfirmed) {
                        // Proceed with the re-open logic
                        fetch(url, {
                            method: 'PATCH',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(response => response.json())
                        .then(ticket_re_open_response => {
                            if (ticket_re_open_response.success == true) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: ticket_re_open_response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    heightAuto: false
                                }).then((result) => {
                                    document.querySelector('.ticket-form-overlay').classList.remove('active');
                                    e.target.reset();
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: ticket_re_open_response.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                confirmButtonText: 'OK',
                                heightAuto: false
                            })
                            console.error(error)
                        });

                    }
                });
    }

    if (e.target && e.target.id === 'add-thermal-event-parts') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn add thêm part này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
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
                        icon:'success',
                        heightAuto: false
                    }).then(()=>{
                    location.reload();
                });
                }
                else {
                    Swal.fire({
                        title:'Error',
                        text:data.message,
                        icon:'error',
                        heightAuto: false
                    }).then(()=>{
                    location.reload();
                });
                }

            })
            .catch(error => {

                Swal.fire({
                    title:'Error',
                    text:error,
                    icon:'error',
                    heightAuto: false
                });

            })
            .finally(() => {

                stopButtonLoading(form);

            });

        });

    }

    if (e.target && e.target.id === 'edit-thermal-event-part-details') {
        e.preventDefault();
        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn edit part này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
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
                        icon:'success',
                        heightAuto: false
                    }).then(()=>{
                    location.reload();
                });
                }
                else {
                    Swal.fire({
                        title:'Error',
                        text:data.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }

            })
            .catch(error => console.error(error));

        });

    }

    if (e.target && e.target.id === 'delete-thermal-event-part-details') {
        e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete this part.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                heightAuto: false
            }).then((result) => 
                {
                    if (result.isConfirmed) {
                        // Proceed with the deletion logic
                        fetch(url, {
                            method: 'PATCH',
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
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                confirmButtonText: 'OK',
                                heightAuto: false
                            })
                            console.error(error)
                        });

                    }
                });
    }

    if (e.target && e.target.id === 'send-approve-thermal-event') {
        e.preventDefault();
        const form = e.target;
        
            Swal.fire({
                title: 'Are you sure?',
                text: "Bạn có muốn send approve cho ticket này ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                heightAuto: false
            }).then((result) => 
                {
                    if (result.isConfirmed) {
                        startButtonLoading(form);
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
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                    confirmButtonText: 'OK',
                                    heightAuto: false
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
                                confirmButtonText: 'OK',
                                heightAuto: false
                            })
                            console.error(error)
                        });

                    }
                });
    }

});


const checkMultipartAffected =
    document.getElementById('multipart_affected_check');

const partsDetailsContainer =
    document.getElementById('thermal_event_parts_details');

if (checkMultipartAffected && partsDetailsContainer) {

    function togglePartFields() {
        const inputs = partsDetailsContainer.querySelectorAll('input');

        if (checkMultipartAffected.value === '1') {

            partsDetailsContainer.style.display = 'block';

            inputs.forEach(input => {
                input.required = true;
                input.disabled = false;
            });

        } else {

            partsDetailsContainer.style.display = 'none';

            inputs.forEach(input => {
                input.required = false;
                input.disabled = true;
            });

        }
    }

    checkMultipartAffected.addEventListener(
        'change',
        togglePartFields
    );

    togglePartFields();
}

document.querySelectorAll('.btn-edit-part').forEach(button => {

    button.addEventListener('click', function () {
        document.getElementById('edit-part-mo-number').value =
            this.dataset.mo;

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
