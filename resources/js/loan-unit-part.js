console.log("JS LOADEDD");


const searchInput = document.getElementById('search-loan-unit-part-receipt-input');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {

        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('#all-loan-unit-part-tickets-table tbody tr');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();

            row.style.display = text.includes(keyword) ? '' : 'none';
        });

    });
}

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
                        text: 'Ticket created successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
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
                        <td>${new_ticket.ticket_owner}</td>
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
                    icon:'error',
                    heightAuto: false
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

    if (e.target && e.target.id === 'edit-loan-unit-part-details') {
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

    if (e.target && e.target.id === 'issue-loan-unit-part') {
        e.preventDefault();
        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn issue unit/part này ?',
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

    if (e.target && e.target.id === 'add-loan-unit-part') {
        e.preventDefault();
        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn thêm unit/part này ?',
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

    if (e.target && e.target.id === 'return-loan-unit-part') {
        e.preventDefault();
        const form = e.target;
        
        Swal.fire({
            title: 'Xác nhận đã hoàn trả unit/part này ?',
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

    if (e.target && e.target.id === 'close-loan-unit-part-ticket') {
        e.preventDefault();
        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn đóng ticket mượn máy/part này ?',
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

    if (e.target && e.target.id === 'cancel-loan-unit-part') {
        e.preventDefault();
        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn cancel part này ?',
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
                method:'PATCH',
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
});


document.querySelectorAll('.btn-edit-part').forEach(button => {

    button.addEventListener('click', function () {

        document.getElementById('edit-part-request-part-details').value =
            this.dataset.part_request;

        document.getElementById('edit-loan-unit-asset-tag').value =
            this.dataset.asset_tag;

        document.getElementById('edit-loan-unit-serial-number').value =
            this.dataset.serial_number;

        document.getElementById('edit-ct-loaned').value =
            this.dataset.ct_loaned;

        document.getElementById('edit-new-ct-return').value =
            this.dataset.new_ct_return;

        document.getElementById('edit-original').value =
            this.dataset.original;    
        
        document.getElementById('edit-start-date').value =
            this.dataset.start_date;

        document.getElementById('edit-end-date').value =
            this.dataset.end_date;

        document.getElementById('edit-loan-unit-part-details').action =
            '/edit-loan-unit-part-details/' + this.dataset.id;

    });

});

document.querySelectorAll('.issue-loan-unit-part-btn').forEach(button => {

    button.addEventListener('click', function () {
        document.getElementById('issue-loan-unit-part').action =
            '/issue-loan-unit-part/' + this.dataset.id;
    });

});

document.querySelectorAll('.return-loan-unit-part-btn').forEach(button => {

    button.addEventListener('click', function () {
        document.getElementById('return-loan-unit-part').action =
            '/return-loan-unit-part/' + this.dataset.id;
    });

});

