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