document.getElementById("discardsubmission").addEventListener("click", function() {
    const userConfirmed = confirm("Your submission will be discarded and you must resubmit before the deadline. Are you sure to discard this submission?");
    if (userConfirmed) {
        fetch('discard_submission.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh on success
                } else {
                    alert(data.message || "Failed to discard submission"); // Show error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while discarding submission');
            });
    }
});