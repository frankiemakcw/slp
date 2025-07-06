document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-form form');
    const filterSelects = filterForm.querySelectorAll('select');
    const downloadAllBtn = document.getElementById('downloadAll');
    const sortByTimeBtn = document.getElementById('sortByTime');
    const sortInput = document.getElementById('sort');
    const statusSelect = document.getElementById('status');
    
    // Function to toggle buttons based on status
    function toggleButtons() {
        const statusFilter = statusSelect.value;
        
        // Show/hide download all button
        if (downloadAllBtn) {
            downloadAllBtn.style.display = statusFilter !== 'not_submitted' ? 'block' : 'none';
        }
        
        // Show/hide sort button
        if (sortByTimeBtn) {
            sortByTimeBtn.style.display = statusFilter !== 'not_submitted' ? 'block' : 'none';
        }
        
        // Reset sorting to default when status is not "submitted"
        if (statusFilter === 'not_submitted' && sortInput.value === 'time_desc') {
            sortInput.value = '';
            // Update button text if visible (though it shouldn't be visible in this case)
            if (sortByTimeBtn) {
                sortByTimeBtn.textContent = 'Sort by Submission Time';
            }
        }
    }
    
    // Auto-submit form when filters change
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            toggleButtons(); // Update button visibility and reset sort if needed
            filterForm.submit();
        });
    });
    
    // Initial button state
    toggleButtons();
    
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
            
            // Only allow sorting if status is "submitted"
            if (statusSelect.value !== 'not_submitted') {
                // Toggle between time sort and class sort
                if (currentSort === 'time_desc') {
                    sortInput.value = ''; // Default sort (class number)
                    sortByTimeBtn.textContent = 'Sort by Submission Time';
                } else {
                    sortInput.value = 'time_desc'; // Newest first
                    sortByTimeBtn.textContent = 'Sort by Class Number';
                }
                
                filterForm.submit();
            }
        });
    }
});