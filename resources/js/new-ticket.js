console.log("JS LOADEDD");




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
        const buttons = this.querySelectorAll('button');
        buttons.forEach(btn => btn.disabled = true);
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
            buttons.forEach(btn => btn.disabled = false);
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


const fileInput = document.getElementById('fileInput');
const fileListUI = document.getElementById('fileList');

let selectedFiles = [];

fileInput.addEventListener('change', function () {
    selectedFiles = Array.from(this.files);
    renderFileList();
});

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

