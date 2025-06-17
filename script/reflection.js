const reflectionText = document.getElementById('reflectionText');
let hasUnsavedChanges = false;
const initialText = reflectionText.value.trim();

function saveToDatabase(reflection) {
    return fetch('save_reflection.php', {
        method: 'POST',
        body: new URLSearchParams({ reflection })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    });
}

// Save button
document.getElementById('btn-save').addEventListener('click', function() {
    const reflection = reflectionText.value.trim();
    hasUnsavedChanges = false;
    if (reflection) {
        saveToDatabase(reflection)
            .then((response) => {
                window.location.href = 'main.php';
            })
            .catch(error => {
                console.error('Error saving:', error);
                alert('Error saving reflection. Please try again.', 'error');
            });
    } else {
        alert('Please enter your reflection before saving.', 'error');
    }
});

// Detect changes in the textarea
reflectionText.addEventListener('input', () => {
    hasUnsavedChanges = (reflectionText.value.trim() !== initialText);
});

// Show browser's default warning if unsaved changes exist
window.addEventListener('beforeunload', (e) => {
    if (hasUnsavedChanges) {
        e.preventDefault();
    }
});