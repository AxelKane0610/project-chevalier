import 'bootstrap/dist/css/bootstrap.min.css';
import '../css/app.css';
import './bootstrap';


import Swal from 'sweetalert2';

import $ from 'jquery';

window.$ = $;
window.jQuery = $;

import select2 from 'select2';
import 'select2/dist/css/select2.min.css';
select2(window, $);



window.Swal = Swal;

Swal.mixin({
  heightAuto: false
});

document.addEventListener("DOMContentLoaded", function() {
    const url = new URL(window.location.href);
    
    // Kiểm tra xem URL có chứa tham số 'token' không (thay 'token' bằng tên param của bạn)
    if (url.searchParams.has('token')) {
        
        // Có thể lưu token vào biến hoặc localStorage để sử dụng cho Fetch API sau này
        // localStorage.setItem('auth_token', url.searchParams.get('token'));
        
        // Xóa token khỏi object URL
        url.searchParams.delete('token');
        
        // Cập nhật lại thanh địa chỉ và lịch sử của trình duyệt
        window.history.replaceState({}, document.title, url.toString());
    }
});

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

// document.addEventListener('DOMContentLoaded', function () {

//     const buttons = document.querySelectorAll('.table-btn');

//     buttons.forEach(button => {
//         button.addEventListener('click', function () {

//             // 1. Lấy tất cả bảng tại thời điểm click
//             const tables = document.querySelectorAll('.ticket-table');

//             // 2. Duyệt qua từng bảng
//             tables.forEach(table => {
//                 // NẾU là bảng always-visible HOẶC có ID của bảng cố định -> BỎ QUA, KHÔNG THÊM d-none
//                 if (table.classList.contains('always-visible') || table.id === 'ttex-tickets-booked-today-container') {
//                     return; 
//                 }

//                 // Với các bảng khác -> Thêm d-none để ẩn
//                 table.classList.add('d-none');
//             });

//             // 3. Hiện bảng target tương ứng với nút vừa bấm
//             const targetId = this.dataset.target;
//             if (targetId) {
//                 const target = document.getElementById(targetId);
//                 if (target) {
//                     target.classList.remove('d-none');
//                 }
//             }

//         });
//     });

// });

document.addEventListener('DOMContentLoaded', function () {
    const openBtns = document.querySelectorAll('.js-input-required-btn');

    openBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Lấy ID form từ data-target của nút vừa nhấn
            const targetId = this.getAttribute('data-target'); 
            const targetForm = document.getElementById(targetId);

            if (targetForm) {
                // Từ thẻ form, tìm ngược lên thẻ cha .ticket-form-overlay gần nhất
                const overlay = targetForm.closest('.ticket-form-overlay');
                overlay.classList.add('active');
            }
        });
    });

    const closeBtns = document.querySelectorAll('.js-close-input-form');

    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Chỉ đóng cái overlay chứa cái nút X vừa được nhấn
            this.closest('.ticket-form-overlay').classList.remove('active');
        });
    });
});


class FileUploader {
    constructor(container) {
        this.container = container;
        this.fileInput = container.querySelector('.file-input');
        this.fileListUI = container.querySelector('.file-list');

        // Đây mới là state chính của toàn bộ file
        this.selectedFiles = [];

        if (this.fileInput) {
            this.initEvents();
        }
    }

    initEvents() {
        this.fileInput.addEventListener('change', (e) => {

            // File mới user vừa chọn
            const newFiles = Array.from(e.target.files);

            newFiles.forEach(file => {

                // Kiểm tra duplicate
                const isDuplicate = this.selectedFiles.some(existingFile =>
                    existingFile.name === file.name &&
                    existingFile.size === file.size &&
                    existingFile.lastModified === file.lastModified
                );

                // Chỉ thêm nếu chưa tồn tại
                if (!isDuplicate) {
                    this.selectedFiles.push(file);
                }
            });

            // Đồng bộ lại input
            this.updateInputFiles();

            // Render UI
            this.renderFileList();
        });
    }

    renderFileList() {
        this.fileListUI.innerHTML = '';

        this.selectedFiles.forEach((file, index) => {

            const li = document.createElement('li');

            li.textContent = file.name;

            const removeBtn = document.createElement('button');

            removeBtn.type = 'button';
            removeBtn.innerHTML = '<i class="ti-close"></i>';

            removeBtn.onclick = () => {
                this.removeFile(index);
            };

            li.appendChild(removeBtn);

            this.fileListUI.appendChild(li);
        });
    }

    removeFile(index) {
        this.selectedFiles.splice(index, 1);

        // Cập nhật lại input
        this.updateInputFiles();

        // Cập nhật UI
        this.renderFileList();
    }

    updateInputFiles() {
        const dataTransfer = new DataTransfer();

        this.selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });

        this.fileInput.files = dataTransfer.files;
    }
}


// Tự động tìm tất cả các cụm upload trên trang và kích hoạt chúng
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.upload-group').forEach(container => {
        new FileUploader(container);
    });
});



window.startButtonLoading = function(form) {

    const buttons = form.querySelectorAll(
        'button[type="submit"], input[type="submit"]'
    );

    buttons.forEach(button => {

        if (!button.dataset.original) {

            button.dataset.original =
                button.tagName === 'BUTTON'
                    ? button.innerHTML
                    : button.value;
        }

        button.disabled = true;

        if (button.tagName === 'BUTTON') {

            button.innerHTML = `
                <span class="spinner"></span>
                Loading...
            `;

        } else {

            button.value = 'Loading...';
        }

    });

    form.dataset.loading = "true";
};

window.stopButtonLoading = function(form) {

    const buttons = form.querySelectorAll(
        'button[type="submit"], input[type="submit"]'
    );

    buttons.forEach(button => {

        button.disabled = false;

        if (button.tagName === 'BUTTON') {

            button.innerHTML =
                button.dataset.original;

        } else {

            button.value =
                button.dataset.original;
        }

    });

    form.dataset.loading = "false";
};

document.addEventListener('DOMContentLoaded', function () {
    // 1. Định nghĩa đầy đủ các phần tử HTML cần dùng
    const btnToggle = document.getElementById('btn-toggle-search');
    const searchDropdown = document.getElementById('search-dropdown');
    const inputReceipt = document.getElementById('input-receipt');
    const btnSubmit = document.getElementById('btn-submit-search'); // <-- ĐÃ SỬA: Thêm biến này bị thiếu
    const resultsArea = document.getElementById('search-results-area'); // <-- ĐÃ SỬA: Thêm biến này bị thiếu
    const searchForm = btnToggle.closest('form'); // Lấy thẻ form bao quanh nếu cần

    // 2. Nhấn nút Search -> Ẩn / Hiện khung dropdown
    btnToggle.addEventListener('click', function (e) {
        searchDropdown.classList.toggle('hidden');
        if (!searchDropdown.classList.contains('hidden')) {
            inputReceipt.focus();
        }
    });

    // 3. Click ra ngoài vùng search thì tự động đóng khung lại
    // document.addEventListener('click', function (e) {
    //     if (!btnToggle.contains(e.target) && !searchDropdown.contains(e.target)) {
    //         searchDropdown.classList.add('hidden');
    //     }
    // });

    // 4. Xử lý khi nhấn nút "Tìm"
    btnSubmit.addEventListener('click', function (e) {
        e.preventDefault(); // <-- ĐÃ SỬA: Chặn form tự load lại trang gây mất kết quả
        const queryVal = inputReceipt.value.trim();
        window.searchReceiptUrl = "/search-receipt"; // <-- ĐÃ SỬA: Đảm bảo URL này khớp với route Laravel của bạn

        if (!queryVal) {
            resultsArea.innerHTML = '<p style="color: red; margin-top: 10px;">Vui lòng nhập mã receipt!</p>';
            return;
        }

        resultsArea.innerHTML = '<p style="margin-top: 10px;">Đang tìm kiếm...</p>';

        // Gọi API Laravel
        fetch(window.searchReceiptUrl + '?receipt=' + encodeURIComponent(queryVal), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    resultsArea.innerHTML = '<p style="margin-top: 10px;">Không tìm thấy kết quả nào trùng khớp.</p>';
                    return;
                }

                // Tạo danh sách kết quả kèm link detail tương ứng với từng loại
                let html = '<ul class="list-group" style="margin-top: 10px; padding-left: 0; list-style: none;">';
                data.forEach(item => {
                    let detailUrl = '';
                    let badgeColor = '';
                    let typeLabel = '';

                    // Định nghĩa URL dựa theo "type" được trả về từ Controller
                    if (item.type === 'software') {
                        detailUrl = `/software-tickets-menu-details/${item.id}`; 
                        badgeColor = '#17a2b8'; // Màu xanh da trời
                        typeLabel = 'Software Ticket';
                    } else if (item.type === 'invoice') {
                        detailUrl = `/invoice-exceptional/${item.id}`; 
                        badgeColor = '#ffc107'; // Màu vàng
                        typeLabel = 'Invoice Exceptional';
                    }

                    html += `
                        <li class="list-group-item" style="padding: 8px 0; border-bottom: 1px solid #eee;">
                            <span class="badge" style="background-color: ${badgeColor}; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 12px; margin-right: 5px;">${typeLabel}</span> 
                            Mã: <strong>${item.ticket_receipt}</strong> - 
                            <a href="${detailUrl}" target="_blank" style="color: #0056b3; text-decoration: underline; font-weight: 500;">
                                Xem chi tiết
                            </a>
                        </li>
                    `;
                });
                html += '</ul>';

                resultsArea.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                resultsArea.innerHTML = '<p style="color: red; margin-top: 10px;">Có lỗi xảy ra trong quá trình tìm kiếm.</p>';
            });
    });
    
    // 5. Nhấn Enter trong ô nhập cũng kích hoạt tìm kiếm mà không load lại trang
    inputReceipt.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // <-- ĐÃ SỬA: Chặn hành vi submit mặc định của form khi gõ Enter
            btnSubmit.click();
        }
    });
});




document.addEventListener('DOMContentLoaded', function () {

    const ajaxTables = new Map();
    let popstateInitialized = false;

    // Kiểm tra đây có phải là navigation bằng Back/Forward không.
    const navigationEntry = performance.getEntriesByType('navigation')[0];
    const isBackForward = navigationEntry?.type === 'back_forward';

    window.initAjaxTable = function (config) {

        const wrapper = document.getElementById(config.wrapper);

        if (!wrapper) return null;

        const container = wrapper.querySelector(config.container);
        const searchInput = wrapper.querySelector('.ajax-search');
        const filters = wrapper.querySelectorAll('.ajax-filter');

        if (!container) return null;

        const tableId = `${window.location.pathname}:${config.key}`;
        const storageKey = `ajax-table:${tableId}`;

        let searchTimer = null;
        let requestController = null;

        function getCurrentState(page = 1) {
            const state = {
                page: String(page),
                search: searchInput ? searchInput.value : '',
                filters: {}
            };

            filters.forEach(filter => {
                if (filter.name) {
                    state.filters[filter.name] = filter.value;
                }
            });

            return state;
        }

        function saveState(state) {
            try {
                sessionStorage.setItem(storageKey, JSON.stringify(state));
            } catch (error) {
                console.error(error);
            }
        }

        // Chỉ khôi phục state được truyền vào.
        // Không tự đọc sessionStorage để tránh lấy nhầm filter cũ.
        function restoreState(state = null) {

            if (!state || typeof state !== 'object') {
                state = {
                    page: '1',
                    search: '',
                    filters: {}
                };
            }

            if (searchInput) {
                searchInput.value = state.search ?? '';
            }

            filters.forEach(filter => {
                filter.value = state.filters?.[filter.name] ?? '';
            });

            return {
                page: String(state.page ?? '1'),
                search: state.search ?? '',
                filters: state.filters ?? {}
            };
        }

        function saveHistory(state, method = 'pushState') {

            saveState(state);

            const currentState = window.history.state || {};

            const historyState = {
                ...currentState,
                ajaxTables: {
                    ...(currentState.ajaxTables || {}),
                    [tableId]: state
                }
            };

            window.history[method](
                historyState,
                '',
                window.location.pathname
            );
        }

        function fetchData(page = 1, options = {}) {

            const {
                history = true,
                state: restoredState = null
            } = options;

            if (restoredState) {
                restoreState(restoredState);
            }

            const state = getCurrentState(page);

            if (history) {
                saveHistory(state);
            } else {
                saveState(state);
            }

            const params = new URLSearchParams();

            params.set('page', state.page);

            if (state.search.trim() !== '') {
                params.set('search', state.search.trim());
            }

            Object.entries(state.filters).forEach(([name, value]) => {
                if (value !== '') {
                    params.set(name, value);
                }
            });

            if (requestController) {
                requestController.abort();
            }

            requestController = new AbortController();

            fetch(`${config.url}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: requestController.signal
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                return response.text();
            })
            .then(html => {
                container.innerHTML = html;
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error(error);
                }
            });
        }

        // Search
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(() => {
                    fetchData(1);
                }, 300);
            });
        }

        // Filters
        filters.forEach(filter => {
            filter.addEventListener('change', () => {
                fetchData(1);
            });
        });

        // Pagination
        container.addEventListener('click', function (event) {
            const link = event.target.closest('.pagination a');

            if (!link) return;

            event.preventDefault();

            const page = new URL(link.href, window.location.origin)
                .searchParams.get('page') || '1';

            fetchData(page);
        });

        const table = {
            key: tableId,
            fetchData,
            restoreState
        };

        ajaxTables.set(tableId, table);

        // Chỉ lấy history.state khi đây là Back/Forward.
        // Các trường hợp khác bắt đầu với trạng thái mặc định.
        const historyState = window.history.state?.ajaxTables?.[tableId];

        let initialState;

        if (isBackForward && historyState) {
            initialState = restoreState(historyState);
        } else {
            initialState = restoreState({
                page: '1',
                search: '',
                filters: {}
            });
        }

        saveState(initialState);

        fetchData(initialState.page, {
            history: false
        });

        // Chỉ đăng ký popstate một lần.
        if (!popstateInitialized) {

            window.addEventListener('popstate', function (event) {

                const states = event.state?.ajaxTables || {};

                ajaxTables.forEach((registeredTable, id) => {

                    // Không dùng sessionStorage làm fallback.
                    const state = states[id] || {
                        page: '1',
                        search: '',
                        filters: {}
                    };

                    const restoredState = registeredTable.restoreState(state);

                    registeredTable.fetchData(restoredState.page, {
                        history: false
                    });
                });
            });

            popstateInitialized = true;
        }

        return table;
    };

});

