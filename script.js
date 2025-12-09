function showForm(formId) {
    document.querySelectorAll(".form-box").forEach(form => form.classList.remove("active"));
    document.getElementById(formId).classList.add("active");
}

function renderDepartments() {
    const departmentList = document.getElementById('department-list'); 
    departmentList.innerHTML = '';
    departments.forEach(department => {
        const listItem = document.createElement('li');
        listItem.textContent = department.name;

        const actionsDiv = document.createElement('div'); 
        actionsDiv.classList.add('actions');

        const editButton = document.createElement('button');
        editButton.textContent = 'Edit';
        editButton.addEventListener('click', () => showEditForm(department)); 
        actionsDiv.appendChild(editButton);

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Delete';
        deleteButton.addEventListener('click', () => deleteDepartment(department.id));
        actionsDiv.appendChild(deleteButton);

        listItem.appendChild(actionsDiv); 
        departmentList.appendChild(listItem);
    });
}

function deleteDepartment(idToDelete) {
    if (confirm('Are you sure you want to delete this department?')) {
        window.location.href = 'delete_department.php?id=' + idToDelete;
    }
}


if (typeof departments !== 'undefined' && document.getElementById('department-list')) {
    renderDepartments();
}