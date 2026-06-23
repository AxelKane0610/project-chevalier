import './bootstrap';
import '../css/app.css';

import Swal from 'sweetalert2';
import $ from 'jquery';

window.$ = $;
window.jQuery = $;


import 'select2/dist/css/select2.min.css';

window.select2 = require('select2');
require('select2/dist/css/select2.min.css');

console.log('jquery:', typeof $);
console.log('select2:', typeof $.fn.select2);


window.Swal = Swal;

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

const fileInput = document.getElementById('fileInput');
const fileListUI = document.getElementById('fileList');

let selectedFiles = [];
if (fileInput) {
    fileInput.addEventListener('change', function () {
        selectedFiles = Array.from(this.files);
        renderFileList();
    });
}
// if (fileInput) {
//     fileInput.addEventListener('change', function () {
//         selectedFiles = Array.from(this.files);
//         renderFileList();
//     });
// }

function renderFileList() {
    fileListUI.innerHTML = ''; //Clear UI cũ (re-render lại từ đầu)

    selectedFiles.forEach((file, index) => {
        const li = document.createElement('li');

        li.textContent = file.name;

        const removeBtn = document.createElement('button');
        // removeBtn.textContent = 'X';
        removeBtn.innerHTML = '<i class="ti-close"></i>';
        removeBtn.onclick = () => removeFile(index);
 
        li.appendChild(removeBtn);
        fileListUI.appendChild(li);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateInputFiles();
    renderFileList();
}

function updateInputFiles() {
    const dataTransfer = new DataTransfer();

    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    fileInput.files = dataTransfer.files;
}


// document.addEventListener('DOMContentLoaded', function () {

//     document.querySelectorAll('form').forEach(form => {

//         form.addEventListener('submit', function () {

//             const submitButtons = form.querySelectorAll(
//                 'button[type="submit"], input[type="submit"]'
//             );

//             submitButtons.forEach(button => {

//                 button.disabled = true;

//                 if (button.tagName === 'BUTTON') {

//                     button.dataset.originalText = button.innerHTML;

//                     button.innerHTML = `
//                         <span class="spinner"></span>
//                         Loading...
//                     `;
//                 } else {

//                     button.dataset.originalText = button.value;
//                     button.value = 'Loading...';
//                 }

//             });

//         });

//     });

// });

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