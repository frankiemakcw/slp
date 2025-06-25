document.addEventListener('DOMContentLoaded', function() {
    const dateTypeRadios = document.querySelectorAll('input[name="date_type"]');
    const singleDateDiv = document.querySelector('.single-date');
    const durationDateDiv = document.querySelector('.duration-date');
    
    dateTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'single') {
                singleDateDiv.style.display = 'block';
                durationDateDiv.style.display = 'none';
                document.getElementById('activity-date').required = true;
                document.getElementById('start-date').required = false;
                document.getElementById('end-date').required = false;
            } else {
                singleDateDiv.style.display = 'none';
                durationDateDiv.style.display = 'block';
                document.getElementById('activity-date').required = false;
                document.getElementById('start-date').required = true;
                document.getElementById('end-date').required = true;
            }
        });
    });
});

document.getElementById('eca-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const dateType = formData.get('date_type');
    const activityId = document.querySelector('input[name="id"]').value;
    
    // Validate dates if duration is selected
    if (dateType === 'duration') {
        const startDate = new Date(formData.get('start_date'));
        const endDate = new Date(formData.get('end_date'));
        
        if (endDate <= startDate) {
            alert('End date must be after start date');
            return;
        }
    }
    
    // Add the ID to the form data if not already included
    if (activityId && !formData.has('id')) {
        formData.append('id', activityId);
    }
    
    // Submit the form
    fetch('update_activity.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = 'main.php'; // Redirect to activities page
        } else {
            alert('Error: ' + (data.message || 'Unknown error occurred'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the activity');
    });
});

// Track if the form has unsaved changes
let formHasChanges = false;

// Detect changes in form inputs
document.getElementById('eca-form').addEventListener('input', () => {
    formHasChanges = true;
});

// Reset flag when form is submitted
document.getElementById('eca-form').addEventListener('submit', () => {
    formHasChanges = false;
});

// Show browser's default warning if there are unsaved changes
window.addEventListener('beforeunload', (e) => {
    if (formHasChanges) {
        e.preventDefault(); 
    }
});