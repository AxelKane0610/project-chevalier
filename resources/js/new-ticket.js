console.log("JS LOADEDD");
// const test = document.querySelector('.js-search-btn').addEventListener('click', alertTest);

// function alertTest() {
//     Swal.fire({
//             title: 'Success!',
//             text: 'Ticket created successfully.',
//             input: 'text',
//             icon: 'success',
//             confirmButtonText: 'OK',
//             scrollbarPadding: false
//         });
// }

// document.querySelectorAll('.js-no-input-required-form').forEach(form => {
//     form.addEventListener('submit', function(e) {
//         e.preventDefault(); // Chặn submit tự động
        
//         Swal.fire({
//             title: 'Bạn có chắc chắn không?',
//             text: "Hành động này không thể hoàn tác!",
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Đồng ý',
//             cancelButtonText: 'Hủy'
//         }).then((result) => {
//             if (result.isConfirmed) {
//                 this.submit(); // Nếu ok thì mới submit form
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

document.addEventListener('submit', function (e) {
    // Kiểm tra xem form nào đang được submit dựa vào ID
    if (e.target && e.target.id === 'create-sw-ticket-form') {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('/create-software-ticket', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(new_ticket => {
            console.log(new_ticket);
            Swal.fire({
                title: 'Success!',
                text: 'Ticket created successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            const newRow = 
            `
                <tr>
                    <td>
                        <a href="/software-tickets-menu-details/${new_ticket.ticket_id}">
                            <i class="ti-arrow-right" ></i>
                        </a>
                    </td>
                    <td>${new_ticket.ticket_reciept}</td>
                    <td>${new_ticket.support_type}</td>
                    <td>${new_ticket.description}</td>
                    <td>${new_ticket.priority}</td>
                </tr>
            
            `;
            document
            .querySelector('#pending-software-tickets-table tbody')
            .insertAdjacentHTML('beforeend', newRow);
            
            document.querySelector('.ticket-form-overlay').classList.remove('active');
            e.target.reset();
        })
        .catch(error => console.error(error));

    }
});




// var softwareTicketForm = document.querySelector('#create-sw-ticket');
// softwareTicketForm.addEventListener('submit', async function(e) {
//     e.preventDefault();

//     fetch('/create-software-ticket', {
//         method: 'POST',
//         body: new FormData(this),
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
//         }
//     })
//     .then(response => response.json())
//     .then(new_ticket => {
//         console.log(new_ticket);
//         Swal.fire({
//             title: 'Success!',
//             text: 'Ticket created successfully.',
//             icon: 'success',
//             confirmButtonText: 'OK'
//         });
//         const newRow = 
//         `
//             <tr>
//                 <td>
//                     <a href="/software-tickets-menu-details/${new_ticket.ticket_id}">
//                         <i class="ti-arrow-right" ></i>
//                     </a>
//                 </td>
//                 <td>${new_ticket.ticket_reciept}</td>
//                 <td>${new_ticket.support_type}</td>
//                 <td>${new_ticket.description}</td>
//                 <td>${new_ticket.priority}</td>
//             </tr>
        
//         `;
//         document
//         .querySelector('#pending-software-tickets-table tbody')
//         .insertAdjacentHTML('beforeend', newRow);
//     })
//     .catch(error => console.error(error));
//     closeTicketForm();
//     softwareTicketForm.reset();
    

//     alert('tạo thành công');
// });