document.addEventListener("DOMContentLoaded", function () {
    const userForm = document.getElementById("userForm");
    const userTable = document.getElementById("userTable");
    let modalUsers = JSON.parse(localStorage.getItem("modalUsers") || "[]");
    let userCount = userTable ? userTable.rows.length : 0;

    if (userTable) {
        modalUsers.forEach(function (user) {
            userCount = Math.max(userCount, Number(user.id) || 0);
        });
    }

    function renderModalUsers() {
        if (!userTable) return;

        userTable.querySelectorAll("[data-stored-user]").forEach(function (row) {
            row.remove();
        });

        modalUsers.forEach(function (user) {
            const newRow = document.createElement("tr");
            newRow.dataset.storedUser = "true";

            const idCell = document.createElement("td");
            const nameCell = document.createElement("td");
            const emailCell = document.createElement("td");
            const roleCell =document.createElement("td");
            const statusCell = document.createElement("td");
            const actionCell = document.createElement("td");

            idCell.textContent = user.id;
            nameCell.textContent = user.name;
            emailCell.textContent = user.email;
            roleCell.textContent =
                typeof user.role === "string" ? user.role : "";

            const badge = document.createElement("span");
            badge.className = "badge bg-success text-white";
            badge.textContent = "Active";
            statusCell.appendChild(badge);

            const viewButton = document.createElement("button");
            viewButton.type = "button";
            viewButton.className = "btn btn-sm btn-info me-2";
            viewButton.textContent = "View";

            const editButton = document.createElement("button");
            editButton.type = "button";
            editButton.className = "btn btn-sm btn-warning";
            editButton.textContent = "Edit";

            const deleteButton = document.createElement("button");
            deleteButton.type="button";
            deleteButton.className="btn btn-sm btn-danger ms-2";
            deleteButton.textContent="Delete";

            deleteButton.addEventListener("click", function () {
                openDeleteModal(user.id, "modal");
            });

            actionCell.appendChild(viewButton);
            actionCell.appendChild(editButton);
            actionCell.appendChild(deleteButton);
            newRow.appendChild(idCell);
            newRow.appendChild(nameCell);
            newRow.appendChild(emailCell);
            newRow.appendChild(roleCell);
            newRow.appendChild(statusCell);
            newRow.appendChild(actionCell);
            userTable.appendChild(newRow);
        });
    }

    renderModalUsers();

    if (userForm && userTable) {
        userForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const nameInput = document.getElementById("userName");
            const emailInput = document.getElementById("userEmail");
            const roleInput = userForm.querySelector("#modalUserRole");
            const name = nameInput ? nameInput.value.trim() : "";
            const email = emailInput ? emailInput.value.trim() : "";
            const role = roleInput ? roleInput.value : "";


            if (!name || !email || !role) {
                alert("Please enter a name, email, and role.");
                return;
            }

            userCount ++;
            const newUser= {
                id:userCount,
                name:name,
                email:email,
                role:role
            };

            modalUsers.push(newUser);
            localStorage.setItem("modalUsers", JSON.stringify(modalUsers));
            renderModalUsers();
            updateDashboardCounts();

            userForm.reset();

            const addUserModalElement = document.getElementById("addUserModal");
            if (addUserModalElement) {
                let addUserModal = bootstrap.Modal.getInstance(addUserModalElement);
                if (!addUserModal) {
                    addUserModal = new bootstrap.Modal(addUserModalElement);
                }
                addUserModal.hide();
            }
        });
    }
    

    const registrationForm = document.getElementById("registrationForm");
    const registeredUsers = document.getElementById("registeredUsers") || document.getElementById("registerdUsers");
    const editUserForm = document.getElementById("editUserForm");
    const editModalElement = document.getElementById("editUserModal");
    const deleteModalElement = document.getElementById("deleteUserModal");

    const editModal = editModalElement ? new bootstrap.Modal(editModalElement) : null;
    const deleteModal = deleteModalElement ? new bootstrap.Modal(deleteModalElement) : null;

    //Load users from Localstorage
    let users = JSON.parse(localStorage.getItem("users")) || [];

    let nextId = Number(localStorage.getItem("nextId")) || 1;
    users.forEach(function (user) {
        nextId = Math.max(nextId, (Number(user.id) || 0) + 1);
    });

    function saveUser() {
        localStorage.setItem("users",JSON.stringify(users));
        localStorage.setItem("nextId",nextId)
    }

    const studentCountElement = document.getElementById("studentCount");
    const teacherCountElement = document.getElementById("teacherCount");
    const baseStudentCount = studentCountElement
        ? Number(studentCountElement.dataset.baseCount) || 0
        : 0;
    const baseTeacherCount = teacherCountElement
        ? Number(teacherCountElement.dataset.baseCount) || 0
        : 0;

    function updateDashboardCounts() {
        if (studentCountElement) {
            studentCountElement.textContent = baseStudentCount + modalUsers.length;
        }

        if (teacherCountElement) {
            const addedTeachers = users.filter(function (user) {
                const role = String(user.role || "").toLowerCase();
                return role === "teacher" || role === "techer";
            }).length;

            teacherCountElement.textContent = baseTeacherCount + addedTeachers;
        }
    }

    function displayUsers() {
        if (!registeredUsers) return;

        registeredUsers.innerHTML = "";

        users.forEach(function (user) {
            const row = document.createElement("tr");
            const idCell = document.createElement("td");
            const nameCell = document.createElement("td");
            const emailCell = document.createElement("td");
            const roleCell = document.createElement("td");
            const actionCell = document.createElement("td");

            idCell.textContent = user.id;
            nameCell.textContent = user.name;
            emailCell.textContent = user.email;
            roleCell.textContent = user.role;

            const editButton = document.createElement("button");
            editButton.type = "button";
            editButton.className = "btn btn-sm btn-warning me-2";
            editButton.textContent = "Edit";
            editButton.addEventListener("click", function () {
                openEditModal(user.id);
            });

            const deleteButton = document.createElement("button");
            deleteButton.type = "button";
            deleteButton.className = "btn btn-sm btn-danger";
            deleteButton.textContent = "Delete";
            deleteButton.addEventListener("click", function () {
                openDeleteModal(user.id, "registered");
            });

            actionCell.appendChild(editButton);
            actionCell.appendChild(deleteButton);

            row.appendChild(idCell);
            row.appendChild(nameCell);
            row.appendChild(emailCell);
            row.appendChild(roleCell);
            row.appendChild(actionCell);

            registeredUsers.appendChild(row);
        });
    }

    function openEditModal(id) {
        if (!editModal) return;

        const user = users.find(function (item) {
            return item.id === id;
        });

        if (!user) return;

        document.getElementById("editUserId").value = user.id;
        document.getElementById("editName").value = user.name;
        document.getElementById("editEmail").value = user.email;
        const role = user.role.toLowerCase();
        document.getElementById("editRole").value =
            role === "techer" ? "teacher" : role;

        editModal.show();
    }

    function openDeleteModal(id, userType) {
        if (!deleteModal) return;

        document.getElementById("deleteUserId").value = id;
        document.getElementById("deleteUserType").value = userType;
        deleteModal.show();
    }

    if (registrationForm) {
        registrationForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const name = (document.getElementById("regName") || document.getElementById("regname")).value.trim();
            const email = (document.getElementById("regEmail") || document.getElementById("regemail")).value.trim();
            const role = (document.getElementById("regRole") || document.getElementById("regrole")).value;

            if (!name || !email || !role) {
                alert("Please fill all fields!");
                return;
            }

            users.push({
                id: nextId++,
                name: name,
                email: email,
                role: role
            });
            saveUser();

            updateDashboardCounts();
            displayUsers();
            registrationForm.reset();
            alert("User registered successfully!");
        });
    }

    if (editUserForm) {
        editUserForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const id = Number(document.getElementById("editUserId").value);
            const user = users.find(function (item) {
                return item.id === id;
            });

            if (!user) return;

            user.name = document.getElementById("editName").value.trim();
            user.email = document.getElementById("editEmail").value.trim();
            user.role = document.getElementById("editRole").value;

            saveUser();
            updateDashboardCounts();
            displayUsers();
            editUserForm.reset();
            editModal.hide();
            alert("User updated successfully!");
        });
    }

    const confirmDeleteButton = document.getElementById("confirmDelete");
    if (confirmDeleteButton) {
        confirmDeleteButton.addEventListener("click", function () {
            const id = Number(document.getElementById("deleteUserId").value);
            const userType = document.getElementById("deleteUserType").value;

            if (userType === "modal") {
                modalUsers = modalUsers.filter(function (user) {
                    return Number(user.id) !== id;
                });
                localStorage.setItem("modalUsers", JSON.stringify(modalUsers));
                renderModalUsers();
            } else {
                users = users.filter(function (user) {
                    return Number(user.id) !== id;
                });

                saveUser();
                displayUsers();
            }

            updateDashboardCounts();
            deleteModal.hide();
            alert("User deleted successfully!");
        });
    }
    updateDashboardCounts();
    displayUsers();
});
