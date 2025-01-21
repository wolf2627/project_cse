function loadRolesForm() {
    const operation = document.getElementById('manage-roles-operation').value;
    const formContainer = document.getElementById('manage-roles-dynamic-form');
    formContainer.innerHTML = ''; // Clear previous form

    if (operation === 'create') {

        // ajax call to the backend for the form
        fetch('/api/app/template/createrole?operation=create')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });


    } else if (operation === 'update') {
        // ajax call to the backend for the form
        fetch('/api/app/template/updaterole?operation=update')
            .then(response => response.text())
            .then(data => {
                formContainer.innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                formContainer.innerHTML = `<div class="alert alert-danger">An error occurred.</div>`;
            });

    } else if (operation === 'delete') {
        // ajax call to the backend for the form
        fetch('/api/app/template/deleterole?operation=delete')
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

function manageRoleSubmitForm(operation) {
    const formData = new FormData(); // Create a FormData object

    let link;  // Declare link here to use it across all cases

    // Add data to FormData based on operation
    if (operation === 'create') {
        formData.append('roleName', document.getElementById('roleName').value);
        formData.append('roleCategory', document.getElementById('roleCategory').value);
        formData.append('description', document.getElementById('description').value);
        link = `/api/app/role/create`;
    } else if (operation === 'update') {
        formData.append('roleId', document.getElementById('roleId').value);
        formData.append('roleName', document.getElementById('roleName').value);
        formData.append('description', document.getElementById('description').value);
        link = `/api/app/role/update`;
    } else if (operation === 'delete') {
        formData.append('roleId', document.getElementById('roleId').value);
        link = `/api/app/role/delete`;
    }

    // Make an AJAX call to the backend
    fetch(link, {
        method: 'POST',
        body: formData, // Send FormData directly
    })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('result');
            if (data.success) {
                const successToast = new Toast('Success', 'now', data.message); // Use const for toast
                successToast.show();
                // Clear the form by reloading its HTML structure
                loadRolesForm();
            } else {
                const failureToast = new Toast('Failure', 'now', data.message); // Fixed typo here
                failureToast.show();
                // Clear the form by reloading its HTML structure
                loadRolesForm();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorToast = new Toast('Error', 'now', 'An error occurred.'); // Use const for toast
            errorToast.show();
            // Clear the form and reload its HTML structure
            loadRolesForm();
        });
}


