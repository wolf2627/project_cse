function loadAssignRoleForms() {
    const operation = document.getElementById('assign-roles-operation').value;
    const formContainer = document.getElementById('assign-roles-dynamic-form');
    formContainer.innerHTML = ''; // Clear previous form

    if (operation === 'assign') {
        console.log('assign clicked');

        // ajax call to the backend for the form
        fetch('/api/app/template/assignrole/type?operation=assign')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });

    } else if (operation === 'unassign') {
        // ajax call to the backend for the form
        fetch('/api/app/template/unassignrole?operation=unassign')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });

    }
}

function loadAssignRoleForms2(){
    const category = document.getElementById('AssignRole-UserType').value;

    const formContainer = document.getElementById('assign-roles-other-fields-dynamic-form');

    formContainer.innerHTML = ''; // Clear previous form

    // ajax call to the backend for the form

    fetch(`/api/app/template/assignrole/otherfields?category=${category}`)
        .then(response => response.text())
        .then(data => {
            formContainer.innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
            formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
        });
}

