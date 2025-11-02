// Global variables
let currentAppId = null;
let currentPage = 1;
let csrfToken = null;

// Initialize on DOM load
document.addEventListener("DOMContentLoaded", function () {
    // Get CSRF token
    csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Get app ID from data attribute
    const appForm = document.getElementById("appForm");
    if (appForm && appForm.dataset.appId) {
        currentAppId = appForm.dataset.appId;
    }

    // Handle database type change to set default ports
    const databaseTypeSelect = document.getElementById("database_type");
    const portInput = document.getElementById("port");

    if (databaseTypeSelect && portInput) {
        databaseTypeSelect.addEventListener("change", function () {
            const defaultPorts = {
                mysql: "3306",
                postgresql: "5432",
                sqlite: "",
                sqlserver: "1433",
                oracle: "1521",
            };

            if (defaultPorts[this.value] !== undefined) {
                portInput.value = defaultPorts[this.value];
                if (this.value === "sqlite") {
                    portInput.disabled = true;
                } else {
                    portInput.disabled = false;
                }
            }
        });
    }

    // Load tables when schema tab is shown
    const schemaTab = document.querySelector('a[href="#schema"]');
    if (schemaTab) {
        schemaTab.addEventListener("shown.bs.tab", function () {
            loadTables(1);
        });
    }

    // Load users when users tab is shown
    const usersTab = document.querySelector('a[href="#users"]');
    if (usersTab) {
        usersTab.addEventListener("shown.bs.tab", function () {
            loadUsers();
        });
    }

    // Add search debounce
    const tableSearch = document.getElementById("tableSearch");
    if (tableSearch) {
        let searchTimeout;
        tableSearch.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadTables(1);
            }, 500);
        });
    }
});

// Connect Database Function
function connectDatabase() {
    const btn = document.getElementById("connectBtn");
    const statusSpan = document.getElementById("connection-status");

    // Get form values
    const databaseType = document.getElementById("database_type").value;
    const databaseName = document.getElementById("database_name").value;
    const host = document.getElementById("host").value;
    const port = document.getElementById("port").value;
    const username = document.getElementById("db_username").value;
    const password = document.getElementById("db_password").value;

    // Validate required fields (password is optional)
    if (!databaseType || !databaseName || !host || !port || !username) {
        statusSpan.innerHTML =
            '<span class="text-danger">Please fill in all required database connection fields</span>';
        return;
    }

    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Connecting...';
    statusSpan.innerHTML =
        '<span class="text-info">Testing connection...</span>';

    // Prepare request body
    const requestBody = {
        database_type: databaseType,
        database_name: databaseName,
        host: host,
        port: port,
        username: username,
    };

    // Only include password if provided
    if (password) {
        requestBody.password = password;
    }

    // Make AJAX request
    fetch(`/apps/${currentAppId}/connect-database`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify(requestBody),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                statusSpan.innerHTML = `<span class="text-info"><i class="material-icons text-sm">sync</i> Connection successful, syncing tables...</span>`;

                // Automatically sync tables after successful connection
                return fetch(`/apps/${currentAppId}/schema/sync`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                })
                    .then((response) => response.json())
                    .then((syncData) => {
                        if (syncData.success) {
                            statusSpan.innerHTML = `<span class="text-success"><i class="material-icons text-sm">check_circle</i> Connected and synced successfully!</span>`;

                            // Update button text
                            btn.innerHTML =
                                '<i class="material-icons me-1" style="font-size: 16px;">cable</i> Reconnect Database';
                            btn.disabled = false;

                            // Reload page to show updated tabs (Schema and potentially Users)
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            statusSpan.innerHTML = `<span class="text-warning"><i class="material-icons text-sm">warning</i> Connected but sync failed. Please refresh and try again.</span>`;
                            btn.innerHTML =
                                '<i class="material-icons me-1" style="font-size: 16px;">cable</i> Reconnect Database';
                            btn.disabled = false;
                        }
                    });
            } else {
                statusSpan.innerHTML = `<span class="text-danger"><i class="material-icons text-sm">error</i> ${data.message}</span>`;
                btn.innerHTML =
                    '<i class="material-icons me-1" style="font-size: 16px;">cable</i> Connect Database';
                btn.disabled = false;
            }
        })
        .catch((error) => {
            console.error("Error connecting to database:", error);
            statusSpan.innerHTML =
                '<span class="text-danger"><i class="material-icons text-sm">error</i> Connection failed. Please try again.</span>';
            btn.innerHTML =
                '<i class="material-icons me-1" style="font-size: 16px;">cable</i> Connect Database';
            btn.disabled = false;
        });
}

// --- Users: Add User modal handling (placeholder) ---
document.addEventListener("DOMContentLoaded", function () {
    const addUserBtn = document.getElementById("addUserBtn");
    const submitAddUserBtn = document.getElementById("submitAddUserBtn");
    const addUserModalEl = document.getElementById("addUserModal");

    if (addUserBtn && addUserModalEl) {
        addUserBtn.addEventListener("click", function () {
            // Reset to create mode
            document.getElementById("editUserId").value = "";
            document.getElementById("addUserModalLabel").textContent =
                "Add User";
            document.getElementById("submitAddUserBtn").textContent =
                "Create User";

            // Show Bootstrap modal
            const modal = new bootstrap.Modal(addUserModalEl);
            // clear form
            const form = document.getElementById("addUserForm");
            if (form) form.reset();
            const alertEl = document.getElementById("addUserAlert");
            if (alertEl) {
                alertEl.style.display = "none";
                alertEl.innerText = "";
            }

            // Load roles into dropdown
            loadRolesForDropdown();

            modal.show();
        });
    }

    if (submitAddUserBtn) {
        submitAddUserBtn.addEventListener("click", function () {
            const name = document.getElementById("newUserName")?.value?.trim();
            const phone = document
                .getElementById("newUserPhone")
                ?.value?.trim();
            const roleId = document
                .getElementById("newUserRole")
                ?.value?.trim();
            const userId = document.getElementById("editUserId")?.value;
            const isEditMode = userId && userId !== "";
            const alertEl = document.getElementById("addUserAlert");

            if (!name || !phone) {
                if (alertEl) {
                    alertEl.className = "alert alert-danger mt-2";
                    alertEl.style.display = "block";
                    alertEl.innerText = "Please provide both name and phone.";
                } else {
                    alert("Please provide both name and phone.");
                }
                return;
            }

            // Disable button while submitting
            submitAddUserBtn.disabled = true;
            submitAddUserBtn.innerHTML = isEditMode
                ? '<span class="spinner-border spinner-border-sm me-2"></span>Updating...'
                : '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

            // Determine endpoint and method
            const url = isEditMode
                ? `/apps/${currentAppId}/users/${userId}`
                : `/apps/${currentAppId}/users`;
            const method = isEditMode ? "PUT" : "POST";

            // Prepare request body
            const requestBody = { name, phone };
            if (roleId) {
                requestBody.role_id = roleId;
            }

            // Call backend to create/update user
            fetch(url, {
                method: method,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(requestBody),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        if (alertEl) {
                            alertEl.className = "alert alert-success mt-2";
                            alertEl.style.display = "block";
                            alertEl.innerText = data.message;
                        }

                        // For create mode: check if this was the first user
                        if (!isEditMode && data.users_count === 1) {
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            // For edit mode or subsequent users: just reload the users list
                            setTimeout(() => {
                                const modal =
                                    bootstrap.Modal.getInstance(addUserModalEl);
                                if (modal) modal.hide();
                                loadUsers();
                            }, 800);
                        }
                    } else {
                        if (alertEl) {
                            alertEl.className = "alert alert-danger mt-2";
                            alertEl.style.display = "block";
                            alertEl.innerText =
                                data.message ||
                                `Failed to ${
                                    isEditMode ? "update" : "create"
                                } user`;
                        }
                        submitAddUserBtn.disabled = false;
                        submitAddUserBtn.innerHTML = isEditMode
                            ? "Update User"
                            : "Create User";
                    }
                })
                .catch((error) => {
                    console.error(
                        `Error ${isEditMode ? "updating" : "creating"} user:`,
                        error
                    );
                    if (alertEl) {
                        alertEl.className = "alert alert-danger mt-2";
                        alertEl.style.display = "block";
                        alertEl.innerText = `Failed to ${
                            isEditMode ? "update" : "create"
                        } user. Please try again.`;
                    }
                    submitAddUserBtn.disabled = false;
                    submitAddUserBtn.innerHTML = isEditMode
                        ? "Update User"
                        : "Create User";
                });
        });
    }
});

// Schema Management Functions
function loadTables(page = 1) {
    if (!currentAppId) {
        return;
    }

    const perPage = document.getElementById("perPageSelect").value;
    const search = document.getElementById("tableSearch").value;

    document.getElementById("schema-loading").style.display = "block";
    document.getElementById("schema-tables-container").style.display = "none";

    fetch(
        `/apps/${currentAppId}/schema/tables?page=${page}&per_page=${perPage}&search=${search}`,
        {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                Accept: "application/json",
            },
        }
    )
        .then((response) => response.json())
        .then((data) => {
            renderTables(data);
            renderPagination(data);
            currentPage = page;

            document.getElementById("schema-loading").style.display = "none";
            document.getElementById("schema-tables-container").style.display =
                "block";
        })
        .catch((error) => {
            console.error("Error loading tables:", error);
            document.getElementById("schema-loading").style.display = "none";
            alert("Failed to load tables. Please try again.");
        });
}

function renderTables(data) {
    const tbody = document.getElementById("schema-tables-body");
    tbody.innerHTML = "";

    if (data.data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <i class="material-icons text-muted" style="font-size: 48px;">table_chart</i>
                    <p class="text-muted mt-3">No tables found</p>
                </td>
            </tr>
        `;
        return;
    }

    data.data.forEach((table) => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>
                <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">${table.table_name}</h6>
                    </div>
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline input-group-sm">
                    <input type="text" class="form-control" id="keywords-${
                        table.id
                    }" value="${
            table.keywords || ""
        }" placeholder="Enter keywords">
                    <button class="btn btn-sm btn-primary mb-0" onclick="updateKeywords(${
                        table.id
                    })">
                        <i class="material-icons text-sm">save</i>
                    </button>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check form-switch d-flex justify-content-center">
                    <input class="form-check-input" type="checkbox" ${
                        table.active_flag ? "checked" : ""
                    } 
                           onchange="toggleActive(${table.id})" id="active-${
            table.id
        }">
                </div>
            </td>
            <td class="text-center">
                <span class="badge badge-sm ${
                    table.active_flag
                        ? "bg-gradient-success"
                        : "bg-gradient-secondary"
                }">
                    ${table.active_flag ? "Active" : "Inactive"}
                </span>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderPagination(data) {
    const pagination = document.getElementById("schema-pagination");

    if (data.last_page <= 1) {
        pagination.innerHTML = "";
        return;
    }

    let html = '<ul class="pagination pagination-sm mb-0">';

    // Previous button
    html += `
        <li class="page-item ${data.current_page === 1 ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="loadTables(${
                data.current_page - 1
            }); return false;">
                <i class="material-icons">chevron_left</i>
            </a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        if (
            i === 1 ||
            i === data.last_page ||
            (i >= data.current_page - 1 && i <= data.current_page + 1)
        ) {
            html += `
                <li class="page-item ${
                    i === data.current_page ? "active" : ""
                }">
                    <a class="page-link" href="#" onclick="loadTables(${i}); return false;">${i}</a>
                </li>
            `;
        } else if (i === data.current_page - 2 || i === data.current_page + 2) {
            html +=
                '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    html += `
        <li class="page-item ${
            data.current_page === data.last_page ? "disabled" : ""
        }">
            <a class="page-link" href="#" onclick="loadTables(${
                data.current_page + 1
            }); return false;">
                <i class="material-icons">chevron_right</i>
            </a>
        </li>
    `;

    html += "</ul>";
    pagination.innerHTML = html;
}

function syncTables() {
    if (!currentAppId) {
        alert("Please create the app first.");
        return;
    }

    const btn = document.getElementById("syncTablesBtn");
    const hasSyncedSchema = btn.dataset.hasSyncedSchema === "true";

    btn.disabled = true;
    btn.innerHTML =
        '<i class="material-icons me-1" style="font-size: 16px;">sync</i> Syncing...';

    fetch(`/apps/${currentAppId}/schema/sync`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                loadTables(currentPage);

                // Check if this was the first sync - reload to show Users tab
                if (!hasSyncedSchema) {
                    alert(
                        "Tables synced successfully! Page will reload to unlock Users tab."
                    );
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alert("Tables synced successfully!");
                }
            } else {
                alert("Failed to sync tables: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error syncing tables:", error);
            alert("Failed to sync tables. Please try again.");
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML =
                '<i class="material-icons me-1" style="font-size: 16px;">sync</i> Sync Tables';
        });
}

function updateKeywords(tableId) {
    if (!currentAppId) return;

    const keywords = document.getElementById(`keywords-${tableId}`).value;

    fetch(`/apps/${currentAppId}/schema/tables/${tableId}/keywords`, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
        body: JSON.stringify({ keywords }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert("Keywords updated successfully!");
            } else {
                alert("Failed to update keywords: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error updating keywords:", error);
            alert("Failed to update keywords. Please try again.");
        });
}

function toggleActive(tableId) {
    if (!currentAppId) return;

    fetch(`/apps/${currentAppId}/schema/tables/${tableId}/toggle`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                loadTables(currentPage);
            } else {
                alert("Failed to toggle active status: " + data.message);
                // Revert checkbox
                const checkbox = document.getElementById(`active-${tableId}`);
                checkbox.checked = !checkbox.checked;
            }
        })
        .catch((error) => {
            console.error("Error toggling active status:", error);
            alert("Failed to toggle active status. Please try again.");
            // Revert checkbox
            const checkbox = document.getElementById(`active-${tableId}`);
            checkbox.checked = !checkbox.checked;
        });
}

// Users Management Functions
function loadUsers() {
    if (!currentAppId) {
        return;
    }

    const usersLoading = document.getElementById("users-loading");
    const usersContainer = document.getElementById("users-container");

    if (usersLoading) usersLoading.style.display = "block";
    if (usersContainer) usersContainer.style.display = "none";

    fetch(`/apps/${currentAppId}/users`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                renderUsers(data.data);
            }
            if (usersLoading) usersLoading.style.display = "none";
            if (usersContainer) usersContainer.style.display = "block";
        })
        .catch((error) => {
            console.error("Error loading users:", error);
            if (usersLoading) usersLoading.style.display = "none";
            alert("Failed to load users. Please try again.");
        });
}

function renderUsers(users) {
    const tbody = document.getElementById("users-table-body");
    if (!tbody) return;

    tbody.innerHTML = "";

    if (users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="material-icons text-muted" style="font-size: 48px;">people</i>
                    <p class="text-muted mt-3">No users found. Click "Add User" to create one.</p>
                </td>
            </tr>
        `;
        return;
    }

    users.forEach((user) => {
        const row = document.createElement("tr");
        const createdDate = new Date(user.created_at).toLocaleDateString();

        row.innerHTML = `
            <td>
                <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">${user.name}</h6>
                    </div>
                </div>
            </td>
            <td>
                <p class="text-xs text-secondary mb-0">${user.phone}</p>
            </td>
            <td>
                <p class="text-xs text-secondary mb-0">${createdDate}</p>
            </td>
            <td class="text-center">
                <span class="badge badge-sm ${
                    user.role_name !== "No Role"
                        ? "bg-gradient-primary"
                        : "bg-gradient-secondary"
                }">${user.role_name}</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-info mb-0 me-1" onclick="editUser(${
                    user.id
                }, '${user.name.replace(/'/g, "\\'")}', '${user.phone}', ${
            user.role_id || "null"
        })" title="Edit user">
                    <i class="material-icons text-sm">edit</i>
                </button>
                <button type="button" class="btn btn-sm btn-danger mb-0" onclick="deleteUser(${
                    user.id
                })" title="Delete user">
                    <i class="material-icons text-sm">delete</i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function deleteUser(userId) {
    if (!confirm("Are you sure you want to delete this user?")) {
        return;
    }

    fetch(`/apps/${currentAppId}/users/${userId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                // Check if this was the last user - reload to hide Roles & Permissions tabs
                if (data.users_count === 0) {
                    alert(
                        "User deleted. Page will reload as this was the last user."
                    );
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    loadUsers();
                }
            } else {
                alert("Failed to delete user: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error deleting user:", error);
            alert("Failed to delete user. Please try again.");
        });
}

function editUser(userId, name, phone, roleId) {
    // Populate modal with user data
    document.getElementById("editUserId").value = userId;
    document.getElementById("newUserName").value = name;
    document.getElementById("newUserPhone").value = phone;

    // Update modal title and button text
    document.getElementById("addUserModalLabel").textContent = "Edit User";
    document.getElementById("submitAddUserBtn").textContent = "Update User";

    // Clear any previous alerts
    const alertEl = document.getElementById("addUserAlert");
    if (alertEl) {
        alertEl.style.display = "none";
        alertEl.innerText = "";
    }

    // Load roles into dropdown and set selected role
    loadRolesForDropdown(() => {
        if (roleId) {
            document.getElementById("newUserRole").value = roleId;
        }
    });

    // Show modal
    const addUserModalEl = document.getElementById("addUserModal");
    const modal = new bootstrap.Modal(addUserModalEl);
    modal.show();
}

// --- Roles Management Functions ---

// Load roles when roles tab is shown
document.addEventListener("DOMContentLoaded", function () {
    const rolesTab = document.querySelector('a[href="#roles"]');
    if (rolesTab) {
        rolesTab.addEventListener("shown.bs.tab", function () {
            loadRoles();
        });
    }
});

// Add Role modal handling
document.addEventListener("DOMContentLoaded", function () {
    const addRoleBtn = document.getElementById("addRoleBtn");
    const submitAddRoleBtn = document.getElementById("submitAddRoleBtn");
    const addRoleModalEl = document.getElementById("addRoleModal");

    if (addRoleBtn && addRoleModalEl) {
        addRoleBtn.addEventListener("click", function () {
            // Reset to create mode
            document.getElementById("editRoleId").value = "";
            document.getElementById("addRoleModalLabel").textContent =
                "Add Role";
            document.getElementById("submitAddRoleBtn").textContent =
                "Create Role";

            // Clear form
            const form = document.getElementById("addRoleForm");
            if (form) form.reset();
            const alertEl = document.getElementById("addRoleAlert");
            if (alertEl) {
                alertEl.style.display = "none";
                alertEl.innerText = "";
            }

            // Show modal
            const modal = new bootstrap.Modal(addRoleModalEl);
            modal.show();
        });
    }

    if (submitAddRoleBtn) {
        submitAddRoleBtn.addEventListener("click", function () {
            const roleName = document
                .getElementById("newRoleName")
                ?.value?.trim();
            const description = document
                .getElementById("newRoleDescription")
                ?.value?.trim();
            const roleId = document.getElementById("editRoleId")?.value;
            const isEditMode = roleId && roleId !== "";
            const alertEl = document.getElementById("addRoleAlert");

            if (!roleName) {
                if (alertEl) {
                    alertEl.className = "alert alert-danger mt-2";
                    alertEl.style.display = "block";
                    alertEl.innerText = "Please provide a role name.";
                } else {
                    alert("Please provide a role name.");
                }
                return;
            }

            // Disable button while submitting
            submitAddRoleBtn.disabled = true;
            submitAddRoleBtn.innerHTML = isEditMode
                ? '<span class="spinner-border spinner-border-sm me-2"></span>Updating...'
                : '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

            // Determine endpoint and method
            const url = isEditMode
                ? `/apps/${currentAppId}/roles/${roleId}`
                : `/apps/${currentAppId}/roles`;
            const method = isEditMode ? "PUT" : "POST";

            // Call backend to create/update role
            fetch(url, {
                method: method,
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    role_name: roleName,
                    description: description,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        if (alertEl) {
                            alertEl.className = "alert alert-success mt-2";
                            alertEl.style.display = "block";
                            alertEl.innerText = data.message;
                        }

                        // Reload roles list and close modal
                        setTimeout(() => {
                            const modal =
                                bootstrap.Modal.getInstance(addRoleModalEl);
                            if (modal) modal.hide();
                            loadRoles();
                        }, 800);
                    } else {
                        if (alertEl) {
                            alertEl.className = "alert alert-danger mt-2";
                            alertEl.style.display = "block";
                            alertEl.innerText =
                                data.message ||
                                `Failed to ${
                                    isEditMode ? "update" : "create"
                                } role`;
                        }
                        submitAddRoleBtn.disabled = false;
                        submitAddRoleBtn.innerHTML = isEditMode
                            ? "Update Role"
                            : "Create Role";
                    }
                })
                .catch((error) => {
                    console.error(
                        `Error ${isEditMode ? "updating" : "creating"} role:`,
                        error
                    );
                    if (alertEl) {
                        alertEl.className = "alert alert-danger mt-2";
                        alertEl.style.display = "block";
                        alertEl.innerText = `Failed to ${
                            isEditMode ? "update" : "create"
                        } role. Please try again.`;
                    }
                    submitAddRoleBtn.disabled = false;
                    submitAddRoleBtn.innerHTML = isEditMode
                        ? "Update Role"
                        : "Create Role";
                });
        });
    }
});

function loadRoles() {
    if (!currentAppId) return;

    document.getElementById("roles-loading").style.display = "block";
    document.getElementById("roles-container").style.display = "none";

    fetch(`/apps/${currentAppId}/roles`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                renderRoles(data.data);
            }
            document.getElementById("roles-loading").style.display = "none";
            document.getElementById("roles-container").style.display = "block";
        })
        .catch((error) => {
            console.error("Error loading roles:", error);
            document.getElementById("roles-loading").style.display = "none";
            alert("Failed to load roles. Please try again.");
        });
}

function renderRoles(roles) {
    const tbody = document.getElementById("roles-table-body");
    tbody.innerHTML = "";

    if (roles.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <i class="material-icons text-muted" style="font-size: 48px;">group</i>
                    <p class="text-muted mt-3">No roles found. Click "Add Role" to create one.</p>
                </td>
            </tr>
        `;
        return;
    }

    roles.forEach((role) => {
        const row = document.createElement("tr");
        const createdDate = new Date(role.created_at).toLocaleDateString();

        row.innerHTML = `
            <td>
                <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">${role.role_name}</h6>
                    </div>
                </div>
            </td>
            <td>
                <p class="text-xs text-secondary mb-0">${
                    role.description || "-"
                }</p>
            </td>
            <td>
                <p class="text-xs text-secondary mb-0">${createdDate}</p>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-info mb-0 me-1" onclick="editRole(${
                    role.id
                }, '${role.role_name.replace(/'/g, "\\'")}', '${(
            role.description || ""
        ).replace(/'/g, "\\'")}')" title="Edit role">
                    <i class="material-icons text-sm">edit</i>
                </button>
                <button type="button" class="btn btn-sm btn-danger mb-0" onclick="deleteRole(${
                    role.id
                })" title="Delete role">
                    <i class="material-icons text-sm">delete</i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function editRole(roleId, roleName, description) {
    // Populate modal with role data
    document.getElementById("editRoleId").value = roleId;
    document.getElementById("newRoleName").value = roleName;
    document.getElementById("newRoleDescription").value = description;

    // Update modal title and button text
    document.getElementById("addRoleModalLabel").textContent = "Edit Role";
    document.getElementById("submitAddRoleBtn").textContent = "Update Role";

    // Clear any previous alerts
    const alertEl = document.getElementById("addRoleAlert");
    if (alertEl) {
        alertEl.style.display = "none";
        alertEl.innerText = "";
    }

    // Show modal
    const addRoleModalEl = document.getElementById("addRoleModal");
    const modal = new bootstrap.Modal(addRoleModalEl);
    modal.show();
}

function deleteRole(roleId) {
    if (!confirm("Are you sure you want to delete this role?")) {
        return;
    }

    fetch(`/apps/${currentAppId}/roles/${roleId}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                loadRoles();
            } else {
                alert("Failed to delete role: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error deleting role:", error);
            alert("Failed to delete role. Please try again.");
        });
}

// Load roles for user modal dropdown
function loadRolesForDropdown(callback) {
    if (!currentAppId) return;

    const roleSelect = document.getElementById("newUserRole");
    if (!roleSelect) return;

    // Keep the first "Select Role (optional)" option and clear the rest
    roleSelect.innerHTML = '<option value="">Select Role (optional)</option>';

    fetch(`/apps/${currentAppId}/roles`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success && data.data.length > 0) {
                data.data.forEach((role) => {
                    const option = document.createElement("option");
                    option.value = role.id;
                    option.textContent = role.role_name;
                    roleSelect.appendChild(option);
                });
            }
            // Call callback if provided
            if (callback && typeof callback === "function") {
                callback();
            }
        })
        .catch((error) => {
            console.error("Error loading roles for dropdown:", error);
        });
}

// --- Permissions Management Functions ---

let currentSelectedRoleId = null;

// Load permissions when permissions tab is shown
document.addEventListener("DOMContentLoaded", function () {
    const permissionsTab = document.querySelector('a[href="#permissions"]');
    if (permissionsTab) {
        permissionsTab.addEventListener("shown.bs.tab", function () {
            loadRolesForPermissions();
        });
    }

    // Handle role selection change
    const roleSelect = document.getElementById("roleSelectPermissions");
    if (roleSelect) {
        roleSelect.addEventListener("change", function () {
            const roleId = this.value;
            if (roleId) {
                currentSelectedRoleId = roleId;
                loadPermissionsForRole(roleId);
            } else {
                currentSelectedRoleId = null;
                showNoRoleSelected();
            }
        });
    }

    // Handle save permissions button
    const savePermissionsBtn = document.getElementById("savePermissionsBtn");
    if (savePermissionsBtn) {
        savePermissionsBtn.addEventListener("click", function () {
            savePermissions();
        });
    }
});

function loadRolesForPermissions() {
    if (!currentAppId) return;

    const roleSelect = document.getElementById("roleSelectPermissions");
    if (!roleSelect) return;

    // Clear and reset
    roleSelect.innerHTML = '<option value="">Choose a role...</option>';
    showNoRoleSelected();

    fetch(`/apps/${currentAppId}/roles`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success && data.data.length > 0) {
                data.data.forEach((role) => {
                    const option = document.createElement("option");
                    option.value = role.id;
                    option.textContent = role.role_name;
                    roleSelect.appendChild(option);
                });
            }
        })
        .catch((error) => {
            console.error("Error loading roles for permissions:", error);
        });
}

function showNoRoleSelected() {
    document.getElementById("permissions-no-role").style.display = "block";
    document.getElementById("permissions-loading").style.display = "none";
    document.getElementById("permissions-container").style.display = "none";
}

function loadPermissionsForRole(roleId) {
    if (!currentAppId || !roleId) return;

    document.getElementById("permissions-no-role").style.display = "none";
    document.getElementById("permissions-loading").style.display = "block";
    document.getElementById("permissions-container").style.display = "none";

    fetch(`/apps/${currentAppId}/roles/${roleId}/permissions`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                renderPermissions(data.role, data.tables);
            }
            document.getElementById("permissions-loading").style.display =
                "none";
            document.getElementById("permissions-container").style.display =
                "block";
        })
        .catch((error) => {
            console.error("Error loading permissions:", error);
            document.getElementById("permissions-loading").style.display =
                "none";
            alert("Failed to load permissions. Please try again.");
        });
}

function renderPermissions(role, tables) {
    const tbody = document.getElementById("permissions-table-body");
    const roleNameSpan = document.getElementById("selectedRoleName");

    roleNameSpan.textContent = role.role_name;
    tbody.innerHTML = "";

    if (tables.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5">
                    <i class="material-icons text-muted" style="font-size: 48px;">table_chart</i>
                    <p class="text-muted mt-3">No active tables found. Please sync schema first.</p>
                </td>
            </tr>
        `;
        return;
    }

    tables.forEach((table) => {
        const row = document.createElement("tr");
        const actions = table.actions || [];

        row.innerHTML = `
            <td>
                <div class="d-flex px-2 py-1">
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">${table.table_name}</h6>
                    </div>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input permission-checkbox" type="checkbox" 
                           data-table="${table.table_name}" data-action="create"
                           ${actions.includes("create") ? "checked" : ""}>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input permission-checkbox" type="checkbox" 
                           data-table="${table.table_name}" data-action="read"
                           ${actions.includes("read") ? "checked" : ""}>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input permission-checkbox" type="checkbox" 
                           data-table="${table.table_name}" data-action="update"
                           ${actions.includes("update") ? "checked" : ""}>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check d-flex justify-content-center">
                    <input class="form-check-input permission-checkbox" type="checkbox" 
                           data-table="${table.table_name}" data-action="delete"
                           ${actions.includes("delete") ? "checked" : ""}>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function savePermissions() {
    if (!currentAppId || !currentSelectedRoleId) return;

    const saveBtn = document.getElementById("savePermissionsBtn");
    saveBtn.disabled = true;
    saveBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    // Collect all permissions from checkboxes
    const permissionsData = {};
    const checkboxes = document.querySelectorAll(".permission-checkbox");

    checkboxes.forEach((checkbox) => {
        const tableName = checkbox.dataset.table;
        const action = checkbox.dataset.action;

        if (!permissionsData[tableName]) {
            permissionsData[tableName] = {
                table_name: tableName,
                actions: [],
            };
        }

        if (checkbox.checked) {
            permissionsData[tableName].actions.push(action);
        }
    });

    // Convert to array
    const permissions = Object.values(permissionsData);

    fetch(`/apps/${currentAppId}/roles/${currentSelectedRoleId}/permissions`, {
        method: "PUT",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ permissions }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert("Permissions saved successfully!");
                loadPermissionsForRole(currentSelectedRoleId);
            } else {
                alert("Failed to save permissions: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error saving permissions:", error);
            alert("Failed to save permissions. Please try again.");
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML =
                '<i class="material-icons me-1" style="font-size: 16px;">save</i> Save Permissions';
        });
}
