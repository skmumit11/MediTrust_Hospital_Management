// Extend session
function extendSession() {
    fetch('../controllers/extendSession.php')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('timeoutLabel').innerText = data.newTimeout + " minutes";
                alert("Session extended successfully!");
            } else {
                alert("Failed to extend session!");
            }
        });
}
