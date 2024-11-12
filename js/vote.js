document.addEventListener('DOMContentLoaded', function() {
    loadNewPeople();
});

let currentPeople = [];

function loadNewPeople() {
    fetch('api/get_people.php')
        .then(response => response.json())
        .then(data => {
            if (data.previouslyCompleted || data.completed) {
                showPreviouslyCompletedMessage();
                return;
            }
            if (data.error) {
                console.error(data.error);
                return;
            }
            currentPeople = data;
            updateDisplay();
        });
}

function showPreviouslyCompletedMessage() {
    const votingArea = document.getElementById('voting-area');
    votingArea.innerHTML = `
        <div class="completion-message">
            <h2>Thank You!</h2>
            <p>You have already completed a voting session.</p>
            <p>Each device is allowed only one voting session.</p>
        </div>
    `;
}

function updateDisplay() {
    for (let i = 0; i < 2; i++) {
        const person = currentPeople[i];
        const container = document.getElementById(`person${i + 1}`);
        
        container.innerHTML = `
            <img src="${person.image_url}" alt="${person.name}" class="person-image">
            <div class="person-info">
                <h3>${person.name}</h3>
                <p>Age: ${person.age}</p>
            </div>
        `;
        
        container.onclick = () => vote(person.id);
    }
}

function vote(personId) {
    const otherPerson = currentPeople.find(p => p.id !== personId);
    
    fetch('api/vote.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ 
            person_id: personId,
            other_person_id: otherPerson.id 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            animateVote(personId);
        } else {
            alert(data.error || 'Error voting');
        }
    });
}

function animateVote(personId) {
    const votedPerson = document.getElementById(`person${currentPeople[0].id === personId ? '1' : '2'}`);
    const otherPerson = document.getElementById(`person${currentPeople[0].id === personId ? '2' : '1'}`);
    
    votedPerson.style.transform = 'scale(1.1)';
    otherPerson.style.opacity = '0.5';
    
    setTimeout(() => {
        votedPerson.style.transform = 'scale(1)';
        otherPerson.style.opacity = '1';
        loadNewPeople();
    }, 1000);
}