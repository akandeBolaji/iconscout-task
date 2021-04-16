let selectedMember;

const editClicked = (member) => {
    selectedMember = member;
    let name = document.getElementById("editName");
    name.placeholder = member.user.name;
    let email = document.getElementById("editEmail");
    email.placeholder = member.user.email;
}

const deleteClicked = (member) => {
    selectedMember = member;
    let text = document.getElementById("deleteText");
    text.innerText = `Are you sure you want to delete member ${member.user.name} with email - ${member.user.email}?`
}

const detailsClicked = (member) => {
    let name = document.getElementById("detailsName");
    let id = document.getElementById("detailsId");
    let email = document.getElementById("detailsEmail");
    name.innerText = `Name - ${member.user.name}`
    id.innerText = `#Member id - ${member.id}`
    email.innerText = `Email - ${member.user.email}`
}

const submitEdit = (event) => {
    event.preventDefault();
    let name = document.getElementById("editName").value
    let email = document.getElementById("editEmail").value
    if (!name && !email) {
        return;
    }
    let data = {
        name:   name
                ? name
                : selectedMember.user.name,
        email: email
                ? email
                : selectedMember.user.email
    }
    fetch(`members/${selectedMember.id}`, {
        method: 'PUT', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch((error) => {
            console.error('Error:', error);
            //TODO implement error
        });
}

const submitNew = (event) => {
    event.preventDefault();
    let name = document.getElementById("newName").value
    let email = document.getElementById("newEmail").value
    let password = document.getElementById("newPassword").value
    if (!name || !email || !password) {
        return;
    }
    let data = {
        name:  name,
        email: email,
        password: password
    }
    fetch('members', {
        method: 'POST', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch((error) => {
            console.error('Error:', error);
            //TODO implement error
        });
}


const submitDelete = () => {
    fetch(`members/${selectedMember.id}`, {
        method: 'DELETE', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            location.reload();
        })
        .catch((error) => {
            console.error('Error:', error);
            //TODO implement error
        });
}
