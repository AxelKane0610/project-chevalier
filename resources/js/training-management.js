document.getElementById('add-row').addEventListener('click', function () {

    let row = `
        <tr>
            <td>
                <input type="text"
                       name="course_id[]"
                       class="form-control"
                       required>
            </td>

            <td>
                <input type="text"
                       name="course_name[]"
                       class="form-control"
                       required>
            </td>

            <td>
                <button type="button" class="btn btn-danger remove-row">
                    X
                </button>
            </td>
        </tr>
    `;

    document.getElementById('course-body').insertAdjacentHTML('beforeend', row);
});


document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();
    }
});


document.addEventListener('DOMContentLoaded', function () {

    const buttons = document.querySelectorAll('.table-btn');
    const tables = document.querySelectorAll('.ticket-table');

    buttons.forEach(button => {

        button.addEventListener('click', function () {

            // Ẩn tất cả bảng
            tables.forEach(table => {
                table.classList.add('d-none');
            });

            // Hiện bảng được chọn
            const target = document.getElementById(this.dataset.target);

            if (target) {
                target.classList.remove('d-none');
            }

        });

    });

});