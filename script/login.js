async function handleCredentialResponse(response) {
    try {
        const serverResponse = await fetch('verify_google_token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ credential: response.credential })
        });
        
        const data = await serverResponse.json();

        if (data.success) {
            window.location.href = 'redirect.php';
        } else {
            alert('Login failed: ' + (data.message || 'Unknown error'));
        }
    } catch (error) {
        console.error("Fetch error:", error);
        alert('Network error occurred');
    }
}