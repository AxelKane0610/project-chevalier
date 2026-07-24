console.log('js loaded');

// const searchInput = document.getElementById('search-spectre-crown-warehouse-input');
// const categoryFilter = document.getElementById('category-filter');
// const warehouseFilter = document.getElementById('warehouse-filter');
// const availabilityFilter = document.getElementById('availability-filter');
// const conditionFilter = document.getElementById('condition-filter');

// function filterTable() {

//     const keyword = searchInput.value.trim().toLowerCase();
//     const category = categoryFilter.value;
//     const warehouse = warehouseFilter.value;
//     const availability = availabilityFilter.value;
//     const condition = conditionFilter.value;

//     const rows = document.querySelectorAll('#spectre-crown-warehouse-items-table tbody tr');

//     rows.forEach(row => {

//         const searchText = row.textContent.toLowerCase();

//         const matchSearch =
//             keyword === '' || searchText.includes(keyword);

//         const matchCategory =
//             category === '' || row.dataset.category === category;

//         const matchWarehouse =
//             warehouse === '' || row.dataset.warehouse === warehouse;

//         const matchAvailability =
//             availability === '' || row.dataset.availability === availability;

//         const matchCondition =
//             condition === '' || row.dataset.condition === condition;

//         row.style.display =
//             (matchSearch &&
//              matchCategory &&
//              matchWarehouse &&
//              matchAvailability &&
//              matchCondition)
//             ? ''
//             : 'none';

//     });

// }

// searchInput.addEventListener('input', filterTable);

// categoryFilter.addEventListener('change', filterTable);
// warehouseFilter.addEventListener('change', filterTable);
// availabilityFilter.addEventListener('change', filterTable);
// conditionFilter.addEventListener('change', filterTable);


// document.addEventListener('DOMContentLoaded', function () {
//     const container = document.getElementById('table-data-container');
//     const searchInput = document.getElementById('search-spectre-crown-warehouse-input');


//     const categoryFilter = document.getElementById('category-filter');
//     const warehouseFilter = document.getElementById('warehouse-filter');
//     const availabilityFilter = document.getElementById('availability-filter');
//     const conditionFilter = document.getElementById('condition-filter');
    
//     // Hàm gửi AJAX lấy dữ liệu
//     function fetchData(page = 1, search = '') {
//         // Gọi URL hiện tại kèm tham số query qua AJAX
//         fetch(`?page=${page}&search=${encodeURIComponent(search)}`, {
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest'
//             }
//         })
//         .then(response => response.text())
//         .then(html => {
//             // Cập nhật lại HTML bảng mà không đổi URL trên thanh địa chỉ
//             container.innerHTML = html;
//         })
//         .catch(error => console.error('Lỗi AJAX:', error));
//     }

//     // 1. Xử lý khi gõ vào ô tìm kiếm (dùng debounce để tránh gọi server liên tục)
//     let timer;
//     searchInput.addEventListener('keyup', function () {
//         clearTimeout(timer);
//         timer = setTimeout(() => {
//             fetchData(1, searchInput.value); // Tìm từ trang 1
//         }, 300); // Đợi 300ms sau khi ngừng gõ mới gửi request
//     });

//     // 2. Bắt sự kiện click vào các nút Pagination
//     container.addEventListener('click', function (e) {
//         // Kiểm tra xem phần tử được click có phải là link chuyển trang không
//         const link = e.target.closest('.pagination a');
        
//         if (link) {
//             e.preventDefault(); // CHẶN reload trang và đổi URL
            
//             // Lấy số trang từ thuộc tính href của nút
//             const url = new URL(link.href);
//             const page = url.searchParams.get('page') || 1;
            
//             // Gọi AJAX với trang mới + từ khóa tìm kiếm hiện tại
//             fetchData(page, searchInput.value);
//         }
//     });
// });

document.addEventListener('DOMContentLoaded', function () {

    const container = document.getElementById('table-data-container');
    const searchInput = document.getElementById('search-spectre-crown-warehouse-input');

    const categoryFilter = document.getElementById('category-filter');
    const warehouseFilter = document.getElementById('warehouse-filter');
    const availabilityFilter = document.getElementById('availability-filter');
    const conditionFilter = document.getElementById('condition-filter');

    // Lấy tất cả giá trị filter hiện tại
    function getFilters() {
        return {
            search: searchInput.value,
            category: categoryFilter.value,
            warehouse: warehouseFilter.value,
            availability: availabilityFilter.value,
            condition: conditionFilter.value
        };
    }

    // Hàm gọi AJAX
    function fetchData(page = 1) {

        const filters = getFilters();

        const params = new URLSearchParams({
            page: page,
            search: filters.search,
            category: filters.category,
            warehouse: filters.warehouse,
            availability: filters.availability,
            condition: filters.condition
        });

        fetch(`?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => console.error('Lỗi AJAX:', error));
    }

    // ===========================
    // SEARCH (Debounce)
    // ===========================
    let timer;

    searchInput.addEventListener('keyup', function () {

        clearTimeout(timer);

        timer = setTimeout(() => {
            fetchData(1);
        }, 300);

    });

    // ===========================
    // FILTERS
    // ===========================
    [
        categoryFilter,
        warehouseFilter,
        availabilityFilter,
        conditionFilter
    ].forEach(filter => {

        filter.addEventListener('change', function () {
            fetchData(1);
        });

    });

    // ===========================
    // PAGINATION
    // ===========================
    container.addEventListener('click', function (e) {

        const link = e.target.closest('.pagination a');

        if (!link) return;

        e.preventDefault();

        const url = new URL(link.href);

        const page = url.searchParams.get('page') || 1;

        fetchData(page);

    });

});
