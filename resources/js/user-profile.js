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
                        text: 'Ticket created successfully.',
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