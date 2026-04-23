import './bootstrap';
import '../css/app.css';

import Swal from 'sweetalert2';
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