document.addEventListener('click', function (e) {

    // ==========================
    // Add Row
    // ==========================
    if (e.target.id === 'add-row') {

        const courseBody = document.getElementById('course-body');

        if (!courseBody) return;

        let row = `
            <tr>
                <td>
                    <input type="text"
                           name="course_id[]"
                           class="form-control"
                           required>
                </td>

                <td>
                    <input type="text"
                           name="course_name[]"
                           class="form-control"
                           required>
                </td>

                <td>
                    <button type="button" class="btn btn-danger remove-row">
                        X
                    </button>
                </td>
            </tr>
        `;

        courseBody.insertAdjacentHTML('beforeend', row);
    }

    // ==========================
    // Remove Row
    // ==========================
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
    }

    // ==========================
    // Switch Table
    // ==========================
    const button = e.target.closest('.table-btn');

    if (button) {

        const tables = document.querySelectorAll('.ticket-table');

        tables.forEach(table => {
            table.classList.add('d-none');
        });

        const target = document.getElementById(button.dataset.target);

        if (target) {
            target.classList.remove('d-none');
        }
    }

});

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-training-request') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn request training ?',
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
            fetch('/request-training', {
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
                    }).then((result) => {
                        location.reload();
                    });

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

    if (e.target && e.target.id === 'edit-ticket-details') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn edit/upload những certificates này ?',
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
            .then(edited_certificates => {

                if (edited_certificates.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: edited_certificates.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title:'Error',
                        text:edited_certificates.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
    }

    if (e.target && e.target.id === 'send-verify-training-ticket') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc các certificates đã được upload đầy đủ ?',
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
            .then(result => {

                if (result.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title:'Error',
                        text:result.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
    }

    if (e.target && e.target.id === 'approve-ticket-form') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn xác nhận user này đã hoàn tất training ?',
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
            .then(result => {

                if (result.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title:'Error',
                        text:result.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
    }

    if (e.target && e.target.id === 'reject-ticket-form') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn reject training cho user này ?',
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
            .then(result => {

                if (result.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title:'Error',
                        text:result.message,
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
    // All Training Courses
    // ==========================================

    initAjaxTable({

        wrapper: 'all-training-courses-container',

        container: '#all-training-courses-table-container',

        url: '/submit-training-menu/filter-all-courses-table'

    });

    // ==========================================
    // Team Tickets
    // ==========================================

    initAjaxTable({

        wrapper: 'all-country-team-training-tickets-container',

        container: '#all-country-team-training-tickets-table-container',

        url: '/submit-training-menu/filter-all-country-team-training-tickets-table'

    });

});