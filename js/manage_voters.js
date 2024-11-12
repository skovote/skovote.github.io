function toggleRevote(deviceId) {
    fetch('../api/toggle_revote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            device_id: deviceId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Error updating permission');
        }
    });
}