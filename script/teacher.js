document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-form form');
    const filterSelects = filterForm.querySelectorAll('select');
    const downloadAllBtn = document.getElementById('downloadAll');
    const sortByTimeBtn = document.getElementById('sortByTime');
    const sortInput = document.getElementById('sort');
    
    // Auto-submit form when filters change
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
    
    // Download all files
    if (downloadAllBtn) {
        downloadAllBtn.addEventListener('click', function() {
            const classFilter = document.getElementById('class').value;
            const statusFilter = document.getElementById('status').value;
            window.location.href = `download_all.php?class=${encodeURIComponent(classFilter)}&status=${encodeURIComponent(statusFilter)}`;
        });
    }

    // Sort by time/class toggle
    if (sortByTimeBtn) {
        sortByTimeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const currentSort = sortInput.value;
            
            // Toggle between time sort and class sort
            if (currentSort === 'time_desc') {
                sortInput.value = ''; // Default sort (class number)
                sortByTimeBtn.textContent = 'Sort by Submission Time';
            } else {
                sortInput.value = 'time_desc'; // Newest first
                sortByTimeBtn.textContent = 'Sort by Class Number';
            }
            
            filterForm.submit();
        });
    }
});