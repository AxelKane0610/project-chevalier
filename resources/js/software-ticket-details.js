console.log("JS LOADED");

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

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    if (e.target && e.target.id === 'close-ticket-form') {
        e.preventDefault();

        const formData = new FormData(e.target);
        const url = e.target.getAttribute('action');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(ticket_complete_response => {
            console.log(ticket_complete_response);
            Swal.fire({
                title: 'Success!',
                text: ticket_complete_response.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                document.querySelector('.ticket-form-overlay').classList.remove('active');
                e.target.reset();
                location.reload();
            });
            
            
            
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error,
                icon: 'error',
                confirmButtonText: 'OK'
            })
            console.error(error)
        });

    }
});