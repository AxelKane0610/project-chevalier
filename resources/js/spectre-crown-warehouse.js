console.log('js loaded');


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
