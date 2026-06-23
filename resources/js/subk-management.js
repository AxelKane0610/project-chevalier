console.log('js loaded');

// Không cần gán window.$ ở đây nữa vì module này sẽ tự hiểu cấu trúc riêng của nó

$(document).ready(function() {
    $('#role-select').select2({
        placeholder: "Chọn quyền hạn...",
        allowClear: true,
        closeOnSelect: false
    });
});