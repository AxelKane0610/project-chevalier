console.log('js loaded');

$(document).ready(function() {
    $('#role-select').select2({
        width: '100%', // Ép Select2 lấy toàn bộ chiều rộng của thẻ cha
        height: 'auto',
        placeholder: "Chọn Roles...",
        allowClear: true
    });
});

$(document).ready(function() {
    $('#role-select-change').select2({
        width: '100%', // Ép Select2 lấy toàn bộ chiều rộng của thẻ cha
        height: 'auto',
        placeholder: "Chọn Roles...",
        allowClear: true
    });
});



$(document).on('click', '.btn-edit-user', function () {

    $('#edit-fullname').val($(this).data('fullname'));
    $('#leader-change').val($(this).data('leaderid'));
    $('#edit-email').val($(this).data('email'));
    $('#edit-phone-number').val($(this).data('phonenumber'));
    $('#edit-learner-id').val($(this).data('learnerid'));
    $('#site-change').val($(this).data('siteid'));
    $('#team-change').val($(this).data('team'));


    let roles = $(this).data('roles');

    // Nếu roles là string JSON
    if (typeof roles === 'string') {
        roles = JSON.parse(roles);
    }

    $('#role-select-change')
        .val(roles)
        .trigger('change');

    document.getElementById('edit-user-info').action =
            '/edit-user-info/' + this.dataset.id;


});

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    
    const formData = new FormData(e.target);
    const url = e.target.getAttribute('action');

    if (e.target && e.target.id === 'create-user-form') {
        e.preventDefault();

        const form = e.target;

        Swal.fire({
            title: 'Bạn có chắc muốn tạo user này ?',
            icon: 'warning',
            showCancelButton: true
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
                        confirmButtonText: 'OK'
                    }).then(()=>{
                        location.reload();
                    });
                } else {
                    Swal.fire({
                            title:'Error',
                            text:new_ticket.message,
                            icon:'error'
                        }).then(()=>{
                        location.reload();
                    });
                }
                
            })
            .catch(error => console.error(error));
        });
    }

    if (e.target && e.target.id === 'edit-user-info') {
        e.preventDefault();

        const form = e.target;
        Swal.fire({
            title: 'Bạn có chắc muốn edit user này ?',
            icon: 'warning',
            showCancelButton: true
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
                        confirmButtonText: 'OK'
                    }).then(()=>{
                        location.reload();
                    });
                } else {
                    Swal.fire({
                            title:'Error',
                            text:new_ticket.message,
                            icon:'error'
                        }).then(()=>{
                        location.reload();
                    });
                }
                
            })
            .catch(error => console.error(error));
        });
    }
});