document.addEventListener('DOMContentLoaded', function() {
    // Multi-select dropdown toggle
    const filterToggle = document.getElementById('filterToggle');
    const filterDropdown = document.getElementById('filterDropdown');

    if (filterToggle && filterDropdown) {
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            filterToggle.classList.toggle('active');
            filterDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!filterToggle.contains(e.target) && !filterDropdown.contains(e.target)) {
                filterToggle.classList.remove('active');
                filterDropdown.classList.remove('active');
            }
        });

        // Prevent closing when clicking inside dropdown
        filterDropdown.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' || e.target.classList.contains('filter-reset')) {
                e.stopPropagation();
            }
        });
    }

    // Table column sorting
    const sortableHeaders = document.querySelectorAll('th.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-column');
            const isAsc = this.classList.contains('asc');
            
            // Remove active class from all headers
            sortableHeaders.forEach(h => {
                h.classList.remove('asc', 'desc');
            });
            
            // Determine sort direction
            const sortDirection = isAsc ? 'desc' : 'asc';
            this.classList.add(sortDirection);
            
            // Build URL with only non-empty filters and sort parameters
            const search = document.querySelector('input[name="search"]').value;
            const category = document.querySelector('input[name="category"]:checked').value;
            const status = document.querySelector('input[name="status"]:checked').value;
            
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (category) params.append('category', category);
            if (status) params.append('status', status);
            params.append('sort_column', column);
            params.append('sort_direction', sortDirection);
            
            // Get base URL from table data attribute
            const table = document.querySelector('table.inventory-table');
            const baseUrl = table ? table.getAttribute('data-route') : '/admin/inventory';
            
            // Navigate to URL with clean query parameters
            window.location.href = baseUrl + (params.toString() ? '?' + params.toString() : '');
        });
    });

    // Restore sort state from query parameters
    const urlParams = new URLSearchParams(window.location.search);
    const sortColumn = urlParams.get('sort_column');
    const sortDirection = urlParams.get('sort_direction');
    if (sortColumn) {
        const header = document.querySelector(`th.sortable[data-column="${sortColumn}"]`);
        if (header) {
            header.classList.add(sortDirection || 'asc');
        }
    }
});
