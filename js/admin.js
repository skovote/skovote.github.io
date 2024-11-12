function deletePerson(id) {
    if (confirm('Are you sure you want to delete this person?')) {
        fetch(`api/delete_person.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                }, 300);
            } else {
                alert('Failed to delete person');
            }
        });
    }
}

function toggleRevote(deviceId) {
    fetch('api/toggle_revote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ device_id: deviceId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const button = document.querySelector(`button[data-device-id="${deviceId}"]`);
            const statusCell = button.parentElement.previousElementSibling;
            if (button.classList.contains('allow')) {
                button.classList.remove('allow');
                button.classList.add('revoke');
                button.textContent = 'Revoke Access';
                statusCell.textContent = 'Available';
            } else {
                button.classList.remove('revoke');
                button.classList.add('allow');
                button.textContent = 'Allow Revote';
                statusCell.textContent = 'Used';
            }
        } else {
            alert('Failed to toggle revote permission');
        }
    });
} 