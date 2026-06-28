console.log('js loaded');
const searchInput = document.getElementById('search-spectre-crown-warehouse-input');
const categoryFilter = document.getElementById('category-filter');
const warehouseFilter = document.getElementById('warehouse-filter');
const availabilityFilter = document.getElementById('availability-filter');
const conditionFilter = document.getElementById('condition-filter');

function filterTable() {

    const keyword = searchInput.value.trim().toLowerCase();
    const category = categoryFilter.value;
    const warehouse = warehouseFilter.value;
    const availability = availabilityFilter.value;
    const condition = conditionFilter.value;

    const rows = document.querySelectorAll('#spectre-crown-warehouse-items-table tbody tr');

    rows.forEach(row => {

        const searchText = row.textContent.toLowerCase();

        const matchSearch =
            keyword === '' || searchText.includes(keyword);

        const matchCategory =
            category === '' || row.dataset.category === category;

        const matchWarehouse =
            warehouse === '' || row.dataset.warehouse === warehouse;

        const matchAvailability =
            availability === '' || row.dataset.availability === availability;

        const matchCondition =
            condition === '' || row.dataset.condition === condition;

        row.style.display =
            (matchSearch &&
             matchCategory &&
             matchWarehouse &&
             matchAvailability &&
             matchCondition)
            ? ''
            : 'none';

    });

}

searchInput.addEventListener('input', filterTable);

categoryFilter.addEventListener('change', filterTable);
warehouseFilter.addEventListener('change', filterTable);
availabilityFilter.addEventListener('change', filterTable);
conditionFilter.addEventListener('change', filterTable);