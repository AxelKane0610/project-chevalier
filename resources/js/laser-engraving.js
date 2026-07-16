console.log("JS LOADEDD");


const searchInput = document.getElementById('search-laser-engraving-input');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {

        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('#all-laser-engraving-tickets-table tbody tr');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();

            row.style.display = text.includes(keyword) ? '' : 'none';
        });

    });
}

document.addEventListener('submit', function (e) {
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    // Kiểm tra xem form nào đang được submit dựa vào ID
    if (e.target && e.target.id === 'create-laser-engraving-ticket-form') {
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
                        text: new_ticket.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    });
                    const newRow = 
                    `
                        <tr>
                            <td>
                                <a href="/laser-engraving-menu-details/${new_ticket.id}">
                                    <button><i class="ti-arrow-right" ></i></button>
                                </a>
                            </td>
                            <td>${new_ticket.receipt}</td>
                            <td>${new_ticket.info_base}</td>
                            <td>${new_ticket.description}</td>
                            <td>${new_ticket.priority}</td>
                            <td>
                        </tr>
                    
                    `;
                    document
                    .querySelector('#pending-laser-engraving-tickets-table tbody')
                    .insertAdjacentHTML('beforeend', newRow);
                    
                    document.querySelector('.ticket-form-overlay').classList.remove('active');
                    e.target.reset();
                    stopButtonLoading(form);
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
        });
        

    }

    if (e.target && e.target.id === 'change-laser-engraving-status-to-in-progress') {
        e.preventDefault();
        Swal.fire({
            title: 'Chuyển ticket sang In Progress ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            heightAuto: false
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Ticket status updated successfully.',
                        timer: 3000, // 3 giây
                        timerProgressBar: true,
                        showConfirmButton: false,
                        heightAuto: false
                    })
                    .then(() => {
                        location.reload();
                    });
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

    if (e.target && e.target.id === 'close-laser-engraving-ticket-form') {
        e.preventDefault();
        const form = e.target;
        Swal.fire({
            title: 'Bạn có chắc muốn close ticket này ?',
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
 
        });
    }

    if (e.target && e.target.id === 're-open-laser-engraving-ticket') {
        e.preventDefault();
        Swal.fire({
            title: 'Bạn có chắc muốn mở lại ticket ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            heightAuto: false
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
                    if (data.success == true) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
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
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            heightAuto: false
                        }).then(()=>{
                            location.reload();
                        });
                    }
                });
            }
        });
    }

    if (e.target && e.target.id === 'edit-laser-engraving-ticket-details') {
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

});
