console.log("JS LOADEDD");

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');
    
    if (e.target && e.target.id === 'change-password') {
        e.preventDefault();

        const form = e.target;
        
        Swal.fire({
            title: 'Bạn có chắc muốn đổi mật khẩu hiện tại ?',
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
                    }).then(()=>{
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
        })

    }

});


document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // CẤU HÌNH & HÀM QUẢN LÝ CHECKBOX STATE
    // ==========================================
    const STORAGE_KEY = 'selected_booking_def_ids';

    function getSavedIds() {
        try {
            return JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || [];
        } catch (e) {
            return [];
        }
    }

    function saveIds(ids) {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }

    function restoreCheckedBoxes() {
        const savedIds = getSavedIds();
        document.querySelectorAll('.booking-def-checkbox').forEach(cb => {
            cb.checked = savedIds.includes(cb.value);
        });
    }

    // 1. Event Delegation cho Checkbox
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

    // 2. Chèn ID vào Form khi Submit
    const bookingForm = document.getElementById('booking-def-part');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function () {
            const allSelectedIds = getSavedIds();
            bookingForm.querySelectorAll('input[name="booking_def[]"]').forEach(el => el.remove());

            allSelectedIds.forEach(id => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'booking_def[]';
                hiddenInput.value = id;
                bookingForm.appendChild(hiddenInput);
            });

            sessionStorage.removeItem(STORAGE_KEY);
        });
    }

    // ==========================================
    // CORE AJAX TABLE ENGINE (KHẮC PHỤC LỖI SEARCH)
    // ==========================================
    function initAjaxTable(config) {
        const wrapper = document.getElementById(config.wrapper);
        if (!wrapper) return;

        const searchInput = wrapper.querySelector('.ajax-search');
        const filters = wrapper.querySelectorAll('.ajax-filter');

        function fetchData(page = 1) {
            // Cố định container lấy từ wrapper hiện tại
            const container = wrapper.querySelector(config.container);
            if (!container) return;

            const params = new URLSearchParams();
            params.append('page', page);

            if (searchInput) {
                params.append('search', searchInput.value.trim());
            }

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
                restoreCheckedBoxes();
            })
            .catch(error => console.error('AJAX Fetch Error:', error));
        }

        // --- BẮT SỰ KIỆN SEARCH ---
        if (searchInput) {
            let timer;

            // ⚡ CHẶN ENTER KHÔNG CHO RELOAD TRANG
            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Ngăn submit form ngoài ý muốn
                    clearTimeout(timer);
                    fetchData(1);
                }
            });

            // ⚡ LẮNG NGHE GÕ TỪ KHÓA & BẤM NÚT XÓA [X]
            ['input', 'search'].forEach(eventType => {
                searchInput.addEventListener(eventType, function () {
                    clearTimeout(timer);
                    timer = setTimeout(() => {
                        fetchData(1);
                    }, 350);
                });
            });
        }

        // --- BẮT SỰ KIỆN FILTER ---
        filters.forEach(filter => {
            filter.addEventListener('change', function () {
                fetchData(1);
            });
        });

        // --- BẮT SỰ KIỆN PAGINATION (DELEGATION TRÊN WRAPPER) ---
        wrapper.addEventListener('click', function (e) {
            const link = e.target.closest('.pagination a');
            if (!link) return;

            e.preventDefault();
            try {
                const url = new URL(link.href);
                const page = url.searchParams.get('page') || 1;
                fetchData(page);
            } catch (err) {
                console.error('Invalid Pagination URL', err);
            }
        });
    }

    restoreCheckedBoxes();

    
    initAjaxTable({
        wrapper: 'all-certificates-table-container',
        container: '.certificates-data-container',
        url: document.getElementById('all-certificates-table-container').dataset.url
    });

});