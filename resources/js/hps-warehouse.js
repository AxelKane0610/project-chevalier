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


// document.addEventListener('change', async function (e) {
//     // Kiểm tra nếu ô vừa thay đổi là Product Number
//     if (e.target.matches('input[name="product_number[]"]')) {
//         const productInput = e.target;
//         const productNumber = productInput.value.trim();
        
//         // Tìm dòng <tr> hiện tại và ô Model tương ứng trong dòng đó
//         const currentRow = productInput.closest('tr');
//         const modelInput = currentRow.querySelector('input[name="model[]"]');

//         // Nếu ô Product Number bị xóa trống
//         if (!productNumber) {
//             modelInput.value = '';
//             modelInput.placeholder = 'Tự động điền...';
//             return;
//         }

//         // Đổi placeholder thông báo trạng thái đang tra cứu
//         modelInput.value = '';
//         modelInput.placeholder = 'Đang tìm...';

//         try {
//             // Gọi API tra cứu thông tin sản phẩm
//             const response = await fetch(`/hps-warehouse-menu/get-model-name?product_number=${encodeURIComponent(productNumber)}`);
//             const data = await response.json();

//             if (response.ok && data.success) {
//                 modelInput.value = data.model;
//             } else {
//                 modelInput.value = '';
//                 modelInput.placeholder = 'Không tìm thấy Model';
//             }
//         } catch (error) {
//             console.error('Lỗi khi tra cứu Model:', error);
//             modelInput.value = '';
//             modelInput.placeholder = 'Lỗi kết nối';
//         }
//     }
// });

document.addEventListener('change', async function (e) {
    
    // ==============================================================
    // TRƯỜNG HỢP 1: Xử lý cho bảng (Code cũ của bạn)
    // ==============================================================
    if (e.target.matches('input[name="product_number[]"]')) {
        const productInput = e.target;
        const productNumber = productInput.value.trim();
        
        const currentRow = productInput.closest('tr');
        const modelInput = currentRow.querySelector('input[name="model[]"]');

        await fetchModelName(productNumber, modelInput);
    }

    // ==============================================================
    // TRƯỜNG HỢP 2: Xử lý cho Form Nhập lại máy (Code mới thêm)
    // ==============================================================
    if (e.target.matches('input[name="re_import_product_number"]')) {
        const productInput = e.target;
        const productNumber = productInput.value.trim();
        
        // Tìm form chứa input này, sau đó tìm ô re_import_model bên trong form
        const form = productInput.closest('form');
        const modelInput = form.querySelector('input[name="re_import_model"]');

        await fetchModelName(productNumber, modelInput);
    }
});

/**
 * Hàm dùng chung để gọi API và điền kết quả vào ô Model
 * Giúp code không bị lặp lại (DRY)
 */
async function fetchModelName(productNumber, modelInput) {
    if (!modelInput) return;

    if (!productNumber) {
        modelInput.value = '';
        modelInput.placeholder = 'Tự động điền...';
        return;
    }

    modelInput.value = '';
    modelInput.placeholder = 'Đang tìm...';

    try {
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


//     initAjaxTable({

//         wrapper: 'all-hps-items-container',

//         container: '#all-hps-items-table-container',

//         url: '/hps-warehouse-menu/filter-all-hps-items'

//     });

//     initAjaxTable({

//         wrapper: 'pending-hps-items-container',

//         container: '#pending-hps-items-table-container',

//         url: '/hps-warehouse-menu/filter-pending-hps-items'

//     });


// });



// document.addEventListener('DOMContentLoaded', function () {

//     const ajaxTables = [];

//     // =========================================
//     // AJAX TABLE INITIALIZATION
//     // =========================================

//     function initAjaxTable(config) {

//         const wrapper = document.getElementById(config.wrapper);

//         if (!wrapper) return null;

//         const container = wrapper.querySelector(config.container);
//         const searchInput = wrapper.querySelector('.ajax-search');
//         const filters = wrapper.querySelectorAll('.ajax-filter');

//         if (!container) return null;

//         const storageKey = `ajax-table-${config.key}`;

//         let searchTimer = null;
//         let requestController = null;

//         // =========================================
//         // GET CURRENT STATE
//         // =========================================

//         function getCurrentState(page = 1) {

//             const state = {
//                 page: String(page),
//                 search: searchInput ? searchInput.value : '',
//                 filters: {}
//             };

//             filters.forEach(filter => {
//                 state.filters[filter.name] = filter.value;
//             });

//             return state;
//         }

//         // =========================================
//         // SAVE STATE TO SESSION STORAGE
//         // =========================================

//         function saveState(state) {

//             sessionStorage.setItem(
//                 storageKey,
//                 JSON.stringify(state)
//             );
//         }

//         // =========================================
//         // RESTORE STATE
//         // =========================================

//         function restoreState(state = null) {

//             if (!state) {
//                 const saved = sessionStorage.getItem(storageKey);

//                 if (saved) {
//                     try {
//                         state = JSON.parse(saved);
//                     } catch (error) {
//                         console.error('Cannot restore AJAX table state:', error);
//                     }
//                 }
//             }

//             if (!state) {
//                 state = {
//                     page: '1',
//                     search: '',
//                     filters: {}
//                 };
//             }

//             if (searchInput) {
//                 searchInput.value = state.search || '';
//             }

//             filters.forEach(filter => {
//                 filter.value = state.filters?.[filter.name] ?? '';
//             });

//             return state;
//         }

//         // =========================================
//         // UPDATE BROWSER HISTORY WITHOUT URL PARAMS
//         // =========================================

//         function saveHistory(state, method = 'pushState') {

//         saveState(state);

//         const historyState = {
//             ...(window.history.state || {}),
//             ajaxTables: {
//                 ...(window.history.state?.ajaxTables || {}),
//                 [config.key]: state
//             }
//         };

//         // Chỉ giữ pathname, loại bỏ toàn bộ query parameters và hash.
//         window.history[method](
//             historyState,
//             '',
//             window.location.pathname
//         );
//     }

//         // =========================================
//         // FETCH DATA
//         // =========================================

//         function fetchData(page = 1, options = {}) {

//             const {
//                 history = true,
//                 state: restoredState = null
//             } = options;

//             if (restoredState) {
//                 restoreState(restoredState);
//             }

//             const state = getCurrentState(page);

//             if (history) {
//                 saveHistory(state, 'pushState');
//             } else {
//                 saveState(state);
//             }

//             const params = new URLSearchParams();

//             params.set('page', state.page);

//             if (state.search.trim() !== '') {
//                 params.set('search', state.search.trim());
//             }

//             Object.entries(state.filters).forEach(([name, value]) => {

//                 if (value !== '') {
//                     params.set(name, value);
//                 }

//             });

//             // Cancel the previous request if it is still running.
//             if (requestController) {
//                 requestController.abort();
//             }

//             requestController = new AbortController();

//             fetch(`${config.url}?${params.toString()}`, {
//                 headers: {
//                     'X-Requested-With': 'XMLHttpRequest'
//                 },
//                 signal: requestController.signal
//             })
//             .then(response => {

//                 if (!response.ok) {
//                     throw new Error(`HTTP error: ${response.status}`);
//                 }

//                 return response.text();
//             })
//             .then(html => {
//                 container.innerHTML = html;
//             })
//             .catch(error => {

//                 if (error.name !== 'AbortError') {
//                     console.error('AJAX table error:', error);
//                 }

//             });
//         }

//         // =========================================
//         // SEARCH
//         // =========================================

//         if (searchInput) {

//             searchInput.addEventListener('input', function () {

//                 clearTimeout(searchTimer);

//                 searchTimer = setTimeout(() => {
//                     fetchData(1);
//                 }, 300);

//             });

//         }

//         // =========================================
//         // FILTER
//         // =========================================

//         filters.forEach(filter => {

//             filter.addEventListener('change', function () {
//                 fetchData(1);
//             });

//         });

//         // =========================================
//         // PAGINATION
//         // =========================================

//         container.addEventListener('click', function (event) {

//             const link = event.target.closest('.pagination a');

//             if (!link) return;

//             event.preventDefault();

//             const url = new URL(link.href, window.location.origin);

//             const page = url.searchParams.get('page') || '1';

//             fetchData(page);
//         });

//         // =========================================
//         // INITIAL STATE
//         // =========================================

//         const initialHistoryState = window.history.state?.ajaxTables?.[config.key];

//         const initialState = restoreState(initialHistoryState || null);

//         const table = {
//             key: config.key,

//             fetchData,

//             restoreState,

//             getCurrentState,

//             getSavedState: function () {
//                 return restoreState(
//                     window.history.state?.ajaxTables?.[config.key] || null
//                 );
//             }
//         };

//         ajaxTables.push(table);

//         // Render the initial table using the restored state.
//         fetchData(initialState.page || 1, {
//             history: false
//         });

//         return table;
//     }

//     // =========================================
//     // INITIALIZE ALL ITEMS
//     // =========================================

//     initAjaxTable({

//         key: 'all',

//         wrapper: 'all-hps-items-container',

//         container: '#all-hps-items-table-container',

//         url: '/hps-warehouse-menu/filter-all-hps-items'

//     });

//     // =========================================
//     // INITIALIZE PENDING ITEMS
//     // =========================================

//     initAjaxTable({

//         key: 'pending',

//         wrapper: 'pending-hps-items-container',

//         container: '#pending-hps-items-table-container',

//         url: '/hps-warehouse-menu/filter-pending-hps-items'

//     });

//     // =========================================
//     // BROWSER BACK / FORWARD
//     // =========================================

//     window.addEventListener('popstate', function (event) {

//         const states = event.state?.ajaxTables || {};

//         ajaxTables.forEach(table => {

//             // Prefer the state belonging to this history entry.
//             // If it has no state yet, restore the last saved state.
//             const state = states[table.key] || null;

//             const restoredState = table.restoreState(state);

//             table.fetchData(restoredState.page || 1, {
//                 history: false
//             });

//         });

//     });

// });


document.addEventListener('DOMContentLoaded', function () {

    window.initAjaxTable({
        key: 'all',
        wrapper: 'all-hps-items-container',
        container: '#all-hps-items-table-container',
        url: '/hps-warehouse-menu/filter-all-hps-items'
    });

    window.initAjaxTable({
        key: 'pending',
        wrapper: 'pending-hps-items-container',
        container: '#pending-hps-items-table-container',
        url: '/hps-warehouse-menu/filter-pending-hps-items'
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


    if (e.target && e.target.id === 're-import-hps-asset') {
        e.preventDefault();
        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn thực hiện nhập lại kho ?',
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