document.addEventListener('click', function (e) {

    if (e.target.id === 'add-row') {

        const courseBody = document.getElementById('items-body');

        if (!courseBody) return;

        let row = `
            <tr>
                <td>
                    <input type="text"
                        name="serial_number[]"
                        class="form-control"
                        required>
                </td>


                <td>
                    <input type="text"
                        name="product_number[]"
                        class="form-control"
                        required>
                </td>

                <td>
                    <input type="text"
                        name="model[]"
                        class="form-control"
                        readonly
                        required>
                </td>

                <td>
                    <input type="text"
                        name="po_number[]"
                        class="form-control"
                        >
                </td>

                <td>
                    <input type="text"
                        name="price[]"
                        class="form-control"
                        >
                </td>

                <td>
                    <input type="text"
                        name="note[]"
                        class="form-control"
                        >
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


document.addEventListener('change', async function (e) {
    // Kiểm tra nếu ô vừa thay đổi là Product Number
    if (e.target.matches('input[name="product_number[]"]')) {
        const productInput = e.target;
        const productNumber = productInput.value.trim();
        
        // Tìm dòng <tr> hiện tại và ô Model tương ứng trong dòng đó
        const currentRow = productInput.closest('tr');
        const modelInput = currentRow.querySelector('input[name="model[]"]');

        // Nếu ô Product Number bị xóa trống
        if (!productNumber) {
            modelInput.value = '';
            modelInput.placeholder = 'Tự động điền...';
            return;
        }

        // Đổi placeholder thông báo trạng thái đang tra cứu
        modelInput.value = '';
        modelInput.placeholder = 'Đang tìm...';

        try {
            // Gọi API tra cứu thông tin sản phẩm
            const response = await fetch(`/hps-warehouse-menu/get-model-name?product_number=${encodeURIComponent(productNumber)}`);
            const data = await response.json();

            if (response.ok && data.success) {
                modelInput.value = data.model;
            } else {
                modelInput.value = '';
                modelInput.placeholder = 'Không tìm thấy Model';
            }
        } catch (error) {
            console.error('Lỗi khi tra cứu Model:', error);
            modelInput.value = '';
            modelInput.placeholder = 'Lỗi kết nối';
        }
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

        wrapper: 'all-hps-items-container',

        container: '#all-hps-items-table-container',

        url: '/hps-warehouse-menu/filter-all-hps-items'

    });


});

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'import-hps-warehouse-item-form') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn nhập kho ?',
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
            fetch('/import-hps-asset', {
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


    if (e.target && e.target.id === 'edit-asset-details') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn edit item này ?',
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

    if (e.target && e.target.id === 'asset-export') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn thực hiện xuất kho ?',
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

});