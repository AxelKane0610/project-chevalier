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