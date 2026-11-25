const adminEmail="admin@college.com";
const adminPassword="Admin@123";
const dashboardSection = document.getElementById("dashboardSection");

if (dashboardSection) {
    let currentUser = null;

    try {
        currentUser = JSON.parse(localStorage.getItem("loggedUser") || "null");
    } catch (error) {
        localStorage.removeItem("loggedUser");
    }

    const isAdmin = currentUser &&
        typeof currentUser.role === "string" &&
        currentUser.role.toLowerCase() === "admin";

    if (!isAdmin) {
        localStorage.removeItem("loggedUser");
        window.location.replace("login.html");
    }
}

document.getElementById("loginForm")?.addEventListener("submit", function(event) {
    event.preventDefault();

    const email = document.getElementById("email").value.trim().toLowerCase();
    const password = document.getElementById("password").value;

    if (email === adminEmail && password === adminPassword) {
        const admin = {
            id: 1,
            name: "admin",
            email: adminEmail,
            role: "Admin"
        };

        localStorage.setItem("loggedUser", JSON.stringify(admin));
        alert("Admin login successful!");
        window.location.replace("index.html");
        return;
    }

    localStorage.removeItem("loggedUser");
    alert("Invalid admin email or password.");
});


 document.getElementById("registerForm")?.addEventListener("submit", function(event){
                        event.preventDefault();

                        let name=document.getElementById("name").value;
                        let email=document.getElementById("email").value;
                        let role=document.getElementById("role").value;
                        let password=document.getElementById("password").value;

                        //get existing users
                        let users=JSON.parse(localStorage.getItem("users")) || [];

                        //check duplicate email
                        let existingUser=users.find(user => user.email.toLowerCase() === email.trim().toLowerCase());

                        if(existingUser){
                            alert("Email already registered!");
                            return;
                        }

                        //create a new user
                        let newUser= {
                            id:Date.now(),
                            name:name,
                            email:email,
                            role:role,
                            password:password
                        };

                        users.push(newUser);

                        //save users in local storage

                        localStorage.setItem("users",JSON.stringify(users));

                        alert("Registration Successfully!");

                        window.location.href="login.html";
            });


 function toggleSidebar() {
                const sidebar=document.getElementById("sidebar");
                const overlay=document.getElementById("sidebarOverlay")

                sidebar.classList.toggle("-translate-x-full");
                overlay.classList.toggle("hidden");
            }
function closeSidebar()
{
    document.getElementById("sidebar").classList.add("-translate-x-full");
    document.getElementById("sidebarOverlay").classList.add("hidden");
}

            function displayUsers(searchText = "") {
                const users = getUsers();
                const countByRole = role =>
                    users.filter(user => String(user.role || "").toLowerCase() === role).length;

                const studentCount = document.getElementById("studentCount");
                const teacherCount = document.getElementById("teacherCount");
                const staffCount = document.getElementById("staffCount");
                const totalUsers = document.getElementById("totalUsers");

                if (studentCount) studentCount.innerText = countByRole("student");
                if (teacherCount) teacherCount.innerText = countByRole("teacher");
                if (staffCount) staffCount.innerText = countByRole("staff");
                if (totalUsers) totalUsers.innerText = users.length;

                const table = document.getElementById("userTable");

                if (!table) return;

                table.innerHTML = "";

                const normalizedSearch = searchText.toLowerCase();
                const filteredUsers = users.filter(user =>
                    String(user.name || "").toLowerCase().includes(normalizedSearch) ||
                    String(user.email || "").toLowerCase().includes(normalizedSearch)
                );

                if (filteredUsers.length === 0) {
                    const row = document.createElement("tr");
                    const cell = document.createElement("td");
                    cell.colSpan = 5;
                    cell.className = "p-6 text-center text-slate-500";
                    cell.textContent = "No registered users found";
                    row.appendChild(cell);
                    table.appendChild(row);
                } else {
                    filteredUsers.forEach(user => {
                        const row = document.createElement("tr");
                        row.className = "transition-colors hover:bg-blue-50";

                        const idCell = document.createElement("td");
                        idCell.className = "px-6 py-4";
                        idCell.textContent = user.id;

                        const nameCell = document.createElement("td");
                        nameCell.className = "px-6 py-4 font-medium text-slate-900";
                        nameCell.textContent = user.name;

                        const emailCell = document.createElement("td");
                        emailCell.className = "px-6 py-4";
                        emailCell.textContent = user.email;

                        const roleCell = document.createElement("td");
                        roleCell.className = "px-6 py-4";
                        const roleBadge = document.createElement("span");
                        const role = String(user.role || "");
                        const roleColor = role.toLowerCase() === "admin"
                            ? "bg-purple-100 text-purple-700"
                            : role.toLowerCase() === "teacher"
                                ? "bg-green-100 text-green-700"
                                : "bg-blue-100 text-blue-700";
                        roleBadge.className = `${roleColor} rounded-full px-3 py-1 text-xs font-semibold`;
                        roleBadge.textContent = role;
                        roleCell.appendChild(roleBadge);

                        const actionsCell = document.createElement("td");
                        actionsCell.className = "px-6 py-4";

                        const editButton = document.createElement("button");
                        editButton.type = "button";
                        editButton.className = "mr-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-700";
                        editButton.textContent = "Edit";
                        editButton.addEventListener("click", () => editUser(user.id));

                        const deleteButton = document.createElement("button");
                        deleteButton.type = "button";
                        deleteButton.className = "rounded-lg bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700";
                        deleteButton.textContent = "Delete";
                        deleteButton.addEventListener("click", () => deleteUser(user.id));

                        actionsCell.append(editButton, deleteButton);
                        row.append(idCell, nameCell, emailCell, roleCell, actionsCell);
                        table.appendChild(row);
                    });
                }

            }

            //load users when page opens
            displayUsers();

            //search functionality

            document.getElementById("searchUser")?.addEventListener("input", function() {
                displayUsers(this.value);
            });

            function logout() {

            localStorage.removeItem("loggedUser");

            alert("Logged out successfully!");

            window.location.href = "login.html";

        }

        function getUsers() {
            return JSON.parse(localStorage.getItem("users")) || [];
        }

        function saveUsers(users) {
            localStorage.setItem("users", JSON.stringify(users));
        }

        //get department from local storage
        function getDepartments(){
            return JSON.parse(localStorage.getItem("departments")) || [];
        }

        function saveDepartments(departments) {
            localStorage.setItem("departments", JSON.stringify(departments));
        }

        function displayDepartments() {
            let departments=getDepartments();
            let table=document.getElementById("departmentTable");

            table.innerHTML="";

            if(departments.length === 0) {

                table.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center p-6 text-gray-500">
                            No departments found
                        </td>
                    </tr>
                `;

        return;
            }

            departments.forEach(function(department){
                let row=document.createElement("tr");
                row.className="transition-colors hover:bg-blue-50";

                //create cells safely
                let idCell = document.createElement("td");
                idCell.className = "px-5 py-4 text-base";
                idCell.textContent = department.id;

                let nameCell = document.createElement("td");
                nameCell.className = "px-5 py-4 font-medium text-gray-800";
                nameCell.textContent = department.name;

                let hodCell = document.createElement("td");
                hodCell.className = "px-5 py-4 font-medium text-gray-800";
                hodCell.textContent = department.hod;

                let actionCell = document.createElement("td");
                actionCell.className = "px-5 py-4";

                actionCell.innerHTML= `
                    <div class="flex gap-2">

                        <button
                            class="bg-green-700 text-white px-3 py-2 rounded-lg
                                hover:bg-green-600"
                            onclick="editDepartment(${department.id})">
                            Edit
                        </button>

                        <button
                            class="bg-red-700 text-white px-3 py-2 rounded-lg
                                hover:bg-red-600"
                            onclick="deleteDepartment(${department.id})">
                            Delete
                        </button>

                    </div>
                `;

                row.append(idCell,nameCell,hodCell,actionCell);
                table.appendChild(row);

            });
        }

        //open and department form
        function openDepartmentForm() {

            document.getElementById("departmentForm").reset();

            document.getElementById("departmentId").value = "";

            document.getElementById("modalTitle").innerText = "Add Department";

            document.getElementById("departmentModal").classList.remove("hidden");

            let modal=document.getElementById("departmentModal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");

        }

        //close department form
        function closeDepartmentForm() {

            let modal=document.getElementById("departmentModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");

            document.getElementById("departmentModal").classList.add("hidden");
        }

        // Add or Update Department
        document.getElementById("departmentForm").addEventListener("submit", function(event) {

            event.preventDefault();

            let id = document.getElementById("departmentId").value;

            let name = document.getElementById("departmentName").value.trim();

            let hod = document.getElementById("hodName").value.trim();

            if(name === "" || hod === ""){
                alert("please fill in all Field");
                return;
            }

            let departments = JSON.parse(localStorage.getItem("departments")) || [];


            

            // Check duplicate department name
            let duplicate = departments.some(department =>
                department.name.toLowerCase() === name.toLowerCase() &&
                String(department.id) !== id
            );

            if (duplicate) {

                alert("This department already exists!");

                return;
            }

            if (id !== "") {

                // Edit existing department
                departments = departments.map(department => {

                    if (String(department.id) === id) {

                        return {
                            ...department,
                            name: name,
                            hod: hod
                        };

                    }

                    return department;

                });

                alert("Department updated successfully!");

            } else {

                // Add new department
                let newDepartment = {

                    id: departments.length > 0
                        ? Math.max(...departments.map(department => Number(department.id))) + 1
                        : 1,

                    name: name,
                    hod: hod

                };

                departments.push(newDepartment);

                alert("Department added successfully!");

            }

            // Save and refresh
            localStorage.setItem("departments", JSON.stringify(departments));

            displayDepartments();

            closeDepartmentForm();

            document.getElementById("departmentForm").reset();


            });



        function editUser(id) {
            const user = getUsers().find(user => String(user.id) === String(id));
            if (!user) return;

            document.getElementById("userEditId").value = user.id;
            document.getElementById("userEditName").value = user.name;
            document.getElementById("userEditEmail").value = user.email;
            document.getElementById("userEditRole").value = user.role;

            const modal = document.getElementById("userEditModal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }

        function closeUserEdit() {
            const modal = document.getElementById("userEditModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }

        document.getElementById("userEditForm")?.addEventListener("submit", function(event) {
            event.preventDefault();

            const id = document.getElementById("userEditId").value;
            const name = document.getElementById("userEditName").value.trim();
            const email = document.getElementById("userEditEmail").value.trim();
            const role = document.getElementById("userEditRole").value;
            const users = getUsers();
            const user = users.find(user => String(user.id) === String(id));

            if (!user) {
                alert("User not found.");
                closeUserEdit();
                return;
            }

            const emailInUse = users.some(existingUser =>
                String(existingUser.id) !== id &&
                existingUser.email.toLowerCase() === email.toLowerCase()
            );

            if (emailInUse) {
                alert("That email address is already registered.");
                return;
            }

            user.name = name;
            user.email = email;
            user.role = role;
            saveUsers(users);

            displayUsers(document.getElementById("searchUser").value);
            closeUserEdit();
            alert("User updated successfully.");
        });

        function deleteUser(id) {
            const users = getUsers();
            const user = users.find(user => String(user.id) === String(id));

            if (!user || !confirm(`Delete ${user.name}? This cannot be undone.`)) {
                return;
            }

            saveUsers(users.filter(user => String(user.id) !== String(id)));
            displayUsers(document.getElementById("searchUser").value);
        }
    // Edit Department
        function editDepartment(id) {

            let departments = getDepartments();

            let department = departments.find(department => String(department.id) === String(id));

            if (!department) return;

            document.getElementById("departmentId").value = department.id;

            document.getElementById("departmentName").value = department.name;

            document.getElementById("hodName").value = department.hod;

            document.getElementById("modalTitle").innerText = "Edit Department";

            let modal = document.getElementById("departmentModal");
            modal.classList.remove("hidden");
            modal.classList.add("flex");

        }



        // Delete Department
            function deleteDepartment(id) {

                if (!confirm("Are you sure you want to delete this department?")) {
                    return;
                }

                let departments = getDepartments();

                departments = departments.filter(department => String(department.id) !== String(id));

                saveDepartments(departments);

                displayDepartments();

                alert("Department deleted successfully!");

            }


            // Display departments when page loads
            displayDepartments();

//defualt admin credentials


