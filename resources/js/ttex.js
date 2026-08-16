console.log("JS LOADED");


document.addEventListener('DOMContentLoaded', function () {

    const buttons = document.querySelectorAll('.table-btn');
    const tables = document.querySelectorAll('.ticket-table:not(.always-visible)');

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





// document.addEventListener('DOMContentLoaded', function () {

//     function initAjaxTable(config) {

//         const wrapper = document.getElementById(config.wrapper);

//         if (!wrapper) return;

//         const container = wrapper.querySelector(config.container);
//         const searchInput = wrapper.querySelector('.ajax-search');
//         const filters = wrapper.querySelectorAll('.ajax-filter');

//         function fetchData(page = 1) {

//             const params = new URLSearchParams();

//             params.append('page', page);

//             // Search
//             if (searchInput) {
//                 params.append('search', searchInput.value);
//             }

//             // Filters
//             filters.forEach(filter => {

//                 if (filter.value !== '') {
//                     params.append(filter.name, filter.value);
//                 }

//             });

//             fetch(`${config.url}?${params.toString()}`, {
//                 headers: {
//                     'X-Requested-With': 'XMLHttpRequest'
//                 }
//             })
//             .then(response => response.text())
//             .then(html => {
//                 container.innerHTML = html;
//             })
//             .catch(error => console.error(error));

//         }

//         // =========================
//         // SEARCH
//         // =========================

//         if (searchInput) {

//             let timer;

//             searchInput.addEventListener('keyup', function () {

//                 clearTimeout(timer);

//                 timer = setTimeout(() => {
//                     fetchData(1);
//                 }, 300);

//             });

//         }

//         // =========================
//         // FILTER
//         // =========================

//         filters.forEach(filter => {

//             filter.addEventListener('change', function () {
//                 fetchData(1);
//             });

//         });

//         // =========================
//         // PAGINATION
//         // =========================

//         container.addEventListener('click', function (e) {

//             const link = e.target.closest('.pagination a');

//             if (!link) return;

//             e.preventDefault();

//             const page = new URL(link.href).searchParams.get('page') || 1;

//             fetchData(page);

//         });

//     }

//     // ==========================================
//     // All TTEX Tickets
//     // ==========================================

//     initAjaxTable({

//         wrapper: 'all-ttex-tickets-container',

//         container: '#all-ttex-tickets-table-container',

//         url: '/ttex-tickets-menu/filter-all-tickets-table'

//     });

//     // ==========================================
//     // Team Tickets
//     // ==========================================

//     initAjaxTable({

//         wrapper: 'ttex-tickets-booked-today-container',

//         container: '#ttex-tickets-booked-today-table-container',

//         url: '/ttex-tickets-menu/filter-tickets-booked-today-table'

//     });

//     initAjaxTable({

//         wrapper: 'pending-def-part-ttex-tickets-container',

//         container: '#pending-def-part-ttex-tickets-table-container',

//         url: '/ttex-tickets-menu/filter-pending-def-part-tickets-table'

//     });

// });

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // CẤU HÌNH & HÀM QUẢN LÝ CHECKBOX STATE
    // ==========================================
    const STORAGE_KEY = 'selected_booking_def_ids';

    function getSavedIds() {
        return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || [];
    }

    function saveIds(ids) {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }

    // Hàm khôi phục trạng thái tích chọn trên giao diện
    function restoreCheckedBoxes() {
        const savedIds = getSavedIds();
        document.querySelectorAll('.booking-def-checkbox').forEach(cb => {
            cb.checked = savedIds.includes(cb.value);
        });
    }

    // 1. Lắng nghe sự kiện tick/untick trên toàn bộ Document (Event Delegation)
    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('booking-def-checkbox')) {
            let savedIds = getSavedIds();
            const value = e.target.value;

            if (e.target.checked) {
                if (!savedIds.includes(value)) savedIds.push(value);
            } else {
                savedIds = savedIds.filter(id => id !== value);
            }
            saveIds(savedIds);
        }
    });

    // 2. Chèn toàn bộ ID đã lưu vào Form khi Submit
    const bookingForm = document.getElementById('booking-def-part');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function () {
            const allSelectedIds = getSavedIds();

            // Xóa các input hidden cũ nếu có
            bookingForm.querySelectorAll('input[name="booking_def[]"]').forEach(el => el.remove());

            // Chèn input hidden cho tất cả ID đã chọn ở mọi trang
            allSelectedIds.forEach(id => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'booking_def[]';
                hiddenInput.value = id;
                bookingForm.appendChild(hiddenInput);
            });

            // Xóa bộ nhớ sau khi submit thành công
            sessionStorage.removeItem(STORAGE_KEY);
        });
    }


    // ==========================================
    // CORE AJAX TABLE ENGINE
    // ==========================================
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

                // === BỔ SUNG: TỰ ĐỘNG TICK LẠI CHECKBOX SAU KHI RENDER HTML MỚI ===
                restoreCheckedBoxes();
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

    // Khôi phục tick chọn cho lần tải trang ban đầu (F5)
    restoreCheckedBoxes();

    // ==========================================
    // All TTEX Tickets
    // ==========================================
    initAjaxTable({
        wrapper: 'all-ttex-tickets-container',
        container: '#all-ttex-tickets-table-container',
        url: '/ttex-tickets-menu/filter-all-tickets-table'
    });

    // ==========================================
    // Team Tickets
    // ==========================================
    initAjaxTable({
        wrapper: 'ttex-tickets-booked-today-container',
        container: '#ttex-tickets-booked-today-table-container',
        url: '/ttex-tickets-menu/filter-tickets-booked-today-table'
    });

    initAjaxTable({
        wrapper: 'pending-def-part-ttex-tickets-container',
        container: '#pending-def-part-ttex-tickets-table-container',
        url: '/ttex-tickets-menu/filter-pending-def-part-tickets-table'
    });

});
