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