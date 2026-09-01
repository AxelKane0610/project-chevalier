import Swal from "sweetalert2";

console.log("JS LOADED");


const searchInput = document.getElementById('search-software-ticket-input');

if (searchInput) {
    searchInput.addEventListener('keyup', function () {

        let keyword = this.value.toLowerCase();
        let rows = document.querySelectorAll('#all-software-tickets-table tbody tr');

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();

            row.style.display = text.includes(keyword) ? '' : 'none';
        });

    });
}

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

    if (e.target && e.target.id === 'create-sw-ticket-form') {
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

            // Confirm mới loading
            startButtonLoading(form);
            fetch('/create-software-ticket', {
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
                    .querySelector('#pending-software-tickets-table tbody')
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

    if (e.target && e.target.id === 'close-ticket-form') 
    {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn đóng ticket này ?',
            icon: 'warning',
            showCancelButton: true,
            heightAuto: false
        })
        .then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            // Confirm mới loading
            startButtonLoading(form);
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json()) 
            .then(ticket_complete_response => { 
                if (ticket_complete_response.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: ticket_complete_response.message,
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
                        text: ticket_complete_response.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then(()=>{
                        location.reload();
                    });
                }
            })
        });

        

    }       


    if (e.target && e.target.id === 'send-approval-form')
    {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Are you sure?',
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
            .then(data => {
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
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }

            })

        });
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
                    }).then(()=>{
                    stopButtonLoading(form);
                });
                }

            })

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

    if (e.target && e.target.id === 'change-ticket-software-status-to-in-progress') {
        e.preventDefault();
        const form = e.target;
        form.dataset.loading = "true";
        Swal.fire({
            title: 'Chuyển ticket sang trạng thái "In Progress" ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
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
                .then(ticket_response => {
                    form.dataset.loading = "false";
                    if (ticket_response.success == true) {
                        Swal.fire({
                            title: 'Success!',
                            text: ticket_response.message,
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
                            text: ticket_response.message,
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

        wrapper: 'all-tickets-container',

        container: '#all-software-tickets-table-container',

        url: '/software-tickets-menu/filter-all-tickets'

    });


});