document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filter-form form');
    const filterSelects = filterForm.querySelectorAll('select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            filterForm.submit();
        });
    });
});