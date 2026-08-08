console.log("JS LOADED");


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

    if (e.target && e.target.id === 'create-ttex-ticket-form') {
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
            fetch('/create-ttex-ticket', {
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
            title: 'Bạn có chắc muốn edit ticket này ?',
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
            .then(edit_ticket => {

                if (edit_ticket.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: edit_ticket.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title:'Error',
                        text: edit_ticket.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
        

    }

    if (e.target && e.target.id === 'close-ttex-ticket') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn close ticket này ?',
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
            .then(ticket => {

                if (ticket.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: ticket.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        window.location.href = `/ttex-tickets-menu`;
                    });

                } else {
                    Swal.fire({
                        title:'Error',
                        text: ticket.message,
                        icon:'error',
                        heightAuto: false
                    });
                    stopButtonLoading(form);
                }
                
            })
            .catch(error => console.error(error));
        });
        

    }

    if (e.target && e.target.id === 'booking-def-part') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn booking những ticket def này ?',
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
            .then(ticket => {

                if (ticket.success === true) {
                    Swal.fire({
                        title: 'Success!',
                        text: ticket.message,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        heightAuto: false
                    }).then((result) => {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title:'Error',
                        text: ticket.message,
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

const checkDefUnusedPartReturn =
    document.getElementById('def_unused_return_check');

const DefUnusedPartReturnContainer =
    document.getElementById('def_part_return_deadline');

if (checkDefUnusedPartReturn && DefUnusedPartReturnContainer) {

    function toggleDefReturnDeadlineField() {
        const inputs = DefUnusedPartReturnContainer.querySelectorAll('input');

        if (checkDefUnusedPartReturn.value === '2' || checkDefUnusedPartReturn.value === '3') {

            def_part_return_deadline.style.display = 'block';

            inputs.forEach(input => {
                input.required = true;
                input.disabled = false;
            });

        } else {

            def_part_return_deadline.style.display = 'none';

            inputs.forEach(input => {
                input.required = false;
                input.disabled = true;
            });

        }
    }

    checkDefUnusedPartReturn.addEventListener(
        'change',
        toggleDefReturnDeadlineField
    );

    toggleDefReturnDeadlineField();
}


document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('all-ttex-tickets-table-container');
    const searchInput = document.getElementById('search-ttex-bill-input-all-tickets-table');

    const statusFilter = document.getElementById('all-ttex-tickets-status-filter');
    const partstatusFilter = document.getElementById('all-ttex-tickets-part-status-filter');
    // Lấy tất cả giá trị filter hiện tại
    function getFilters() {
        return {
            search: searchInput.value,
            status: statusFilter.value,
            partStatus: partstatusFilter.value
        };
    }

    // Hàm gọi AJAX
    function fetchData(page = 1) {

        const filters = getFilters();

        const params = new URLSearchParams({
            page: page,
            search: filters.search,
            status: filters.status,
            partStatus: filters.partStatus
        });

        fetch(`/ttex-tickets-menu/filter-all-tickets-table?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => console.error('Lỗi AJAX:', error));
    }

    // ===========================
    // SEARCH (Debounce)
    // ===========================
    let timer;

    searchInput.addEventListener('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(() => {
            fetchData(1);
        }, 300);

    });

    // ===========================
    // FILTERS
    // ===========================
    [
        statusFilter,
        partstatusFilter
        
    ].forEach(filter => {

        filter.addEventListener('change', function () {
            fetchData(1);
        });

    });

    // ===========================
    // PAGINATION
    // ===========================
    container.addEventListener('click', function (e) {

        const link = e.target.closest('.pagination a');

        if (!link) return;

        e.preventDefault();

        const url = new URL(link.href);

        const page = url.searchParams.get('page') || 1;

        fetchData(page);

    });

});