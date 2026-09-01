console.log('JS Loaded');

document.addEventListener('DOMContentLoaded', function () {

    const buttons = document.querySelectorAll('.table-btn');
    const tables = document.querySelectorAll('.ticket-table');

    buttons.forEach(button => {

        button.addEventListener('click', function () {

            // Ẩn tất cả bảng
            tables.forEach(table => {
                table.classList.add('d-none');
            });

            // Hiện bảng được chọn
            const target = document.getElementById(this.dataset.target);

            if (target) {
                target.classList.remove('d-none');
            }

        });

    });

});


document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-out-of-office-ticket-form') {
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
                    console.log(new_ticket);
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

    if (e.target && e.target.id === 'send-approve-out-of-office-ticket') {
        e.preventDefault();

        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn request approve nghỉ phép ?',
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

    if (e.target && e.target.id === 'approve-out-of-office-ticket') {
        e.preventDefault();

        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn approve cho ticket này ?',
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

    if (e.target && e.target.id === 'reject-out-of-office-ticket') {
        e.preventDefault();

        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn reject ticket này ?',
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

    if (e.target && e.target.id === 're-open-out-of-office-ticket') {
        e.preventDefault();

        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn mở lại ticket này ?',
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
});

document.addEventListener('DOMContentLoaded', function () {

    function initAjaxTable(config) {

        const wrapper = document.getElementById(config.wrapper);

        if (!wrapper) return;

        const container = wrapper.querySelector(config.container);
        const searchInput = wrapper.querySelector('.ajax-search');
        const filters = wrapper.querySelectorAll('.ajax-filter');

        function fetchData(page = 1) {

            const params = new URLSearchParams();

            params.append('page', page);

            // Search
            if (searchInput) {
                params.append('search', searchInput.value);
            }

            // Filters
            filters.forEach(filter => {

                if (filter.value !== '') {
                    params.append(filter.name, filter.value);
                }

            });

            fetch(`${config.url}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => console.error(error));

        }

        // =========================
        // SEARCH
        // =========================

        if (searchInput) {

            let timer;

            searchInput.addEventListener('keyup', function () {

                clearTimeout(timer);

                timer = setTimeout(() => {
                    fetchData(1);
                }, 300);

            });

        }

        // =========================
        // FILTER
        // =========================

        filters.forEach(filter => {

            filter.addEventListener('change', function () {
                fetchData(1);
            });

        });

        // =========================
        // PAGINATION
        // =========================

        container.addEventListener('click', function (e) {

            const link = e.target.closest('.pagination a');

            if (!link) return;

            e.preventDefault();

            const page = new URL(link.href).searchParams.get('page') || 1;

            fetchData(page);

        });

    }

    // ==========================================
    // Individual Tickets
    // ==========================================

    initAjaxTable({

        wrapper: 'all-out-of-office-tickets-container',

        container: '#all-out-of-office-tickets-table-container',

        url: '/out-of-office-tickets-menu/filter-all-tickets'

    });


});