<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='apps'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Create App"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('apps.store') }}" method="POST" id="appForm">
                        @csrf
                        <div class="card">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="text-white text-capitalize ps-3">Create New App</h6>
                                        <div class="me-3">
                                            <a href="{{ route('apps.index') }}" class="btn btn-outline-white btn-sm mb-0">
                                                <i class="material-icons text-sm">arrow_back</i> Back to Apps
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Navigation Tabs -->
                                <div class="nav-wrapper position-relative end-0">
                                    <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#app-details" role="tab" aria-controls="app-details" aria-selected="true">
                                                <i class="material-icons text-lg position-relative">apps</i>
                                                <span class="ms-1">App Details</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#schema" role="tab" aria-controls="schema" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">account_tree</i>
                                                <span class="ms-1">Schema</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">people</i>
                                                <span class="ms-1">Users</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#roles" role="tab" aria-controls="roles" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">group</i>
                                                <span class="ms-1">Roles</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">security</i>
                                                <span class="ms-1">Permissions</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                
                                <!-- Tab Content -->
                                <div class="tab-content" id="tabs-tabContent">
                                    <!-- App Details Tab -->
                                    <div class="tab-pane fade show active" id="app-details" role="tabpanel" aria-labelledby="app-details-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <h6 class="text-primary">App Details</h6>
                                                <hr class="horizontal dark">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('app_name') is-focused is-filled @enderror">
                                                    <label class="form-label">App Name *</label>
                                                    <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror" value="{{ old('app_name') }}" required>
                                                    @error('app_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('phone_number') is-focused is-filled @enderror">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}">
                                                    @error('phone_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-3 @error('description') is-focused is-filled @enderror">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <h6 class="text-primary">Database Connection</h6>
                                                <hr class="horizontal dark">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('database_type') is-focused is-filled @enderror">
                                                    <select name="database_type" class="form-control @error('database_type') is-invalid @enderror" required>
                                                        <option value="">Select Database Type</option>
                                                        <option value="mysql" {{ old('database_type') == 'mysql' ? 'selected' : '' }}>MySQL</option>
                                                        <option value="postgresql" {{ old('database_type') == 'postgresql' ? 'selected' : '' }}>PostgreSQL</option>
                                                        <option value="sqlite" {{ old('database_type') == 'sqlite' ? 'selected' : '' }}>SQLite</option>
                                                        <option value="sqlserver" {{ old('database_type') == 'sqlserver' ? 'selected' : '' }}>SQL Server</option>
                                                        <option value="oracle" {{ old('database_type') == 'oracle' ? 'selected' : '' }}>Oracle</option>
                                                    </select>
                                                    @error('database_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('database_name') is-focused is-filled @enderror">
                                                    <label class="form-label">Database Name *</label>
                                                    <input type="text" name="database_name" class="form-control @error('database_name') is-invalid @enderror" value="{{ old('database_name') }}" required>
                                                    @error('database_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
<div class="input-group input-group-outline mb-3 @if(old('host', 'localhost')) is-filled @endif @error('host') is-focused @enderror">
    <label class="form-label">Host *</label>
    <input type="text" name="host" class="form-control @error('host') is-invalid @enderror" value="{{ old('host', 'localhost') }}" required>
    @error('host')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 is-focused is-filled @error('port') is-invalid @enderror">
                                                    <label class="form-label">Port *</label>
                                                    <input type="number" name="port" class="form-control @error('port') is-invalid @enderror" value="{{ old('port', '3306') }}" required min="1" max="65535">
                                                    @error('port')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('username') is-focused is-filled @enderror">
                                                    <label class="form-label">Username *</label>
                                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                                    @error('username')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('password') is-focused is-filled @enderror">
                                                    <label class="form-label">Password *</label>
                                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Schema Tab -->
                                    <div class="tab-pane fade" id="schema" role="tabpanel" aria-labelledby="schema-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="text-center py-5">
                                                    <i class="material-icons text-muted" style="font-size: 64px;">account_tree</i>
                                                    <h5 class="text-muted mt-3">Schema Management</h5>
                                                    <p class="text-muted">Schema management features will be implemented here.</p>
                                                    <p class="text-sm text-secondary">This section will allow you to manage database schemas, tables, and relationships.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Users Tab -->
                                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="text-primary mb-1">App Users</h5>
                                                        <p class="text-sm text-muted mb-0">Add users who will have access to this application</p>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center" onclick="addUser()">
                                                        <i class="material-icons me-1" style="font-size: 16px;">add</i>
                                                        Add User
                                                    </button>
                                                </div>
                                                
                                                <div id="users-container">
                                                    <!-- Default user -->
                                                    <div class="user-row mb-4" data-user-index="0">
                                                        <div class="card border">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="icon icon-sm icon-shape bg-gradient-primary shadow text-center me-3">
                                                                            <i class="material-icons opacity-10 text-white">person</i>
                                                                        </div>
                                                                        <h6 class="mb-0 text-dark">User #1</h6>
                                                                    </div>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center" onclick="removeUser(0)" style="display: none;">
                                                                        <i class="material-icons me-1" style="font-size: 16px;">delete</i>
                                                                        Remove
                                                                    </button>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                        <div class="input-group input-group-outline mb-3">
                                                                            <label class="form-label">Full Name *</label>
                                                                            <input type="text" name="users[0][name]" class="form-control" required>
                                                                        </div>
                                                                        <small class="text-muted">Enter the user's full name</small>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="input-group input-group-outline mb-3 is-focused is-filled">
                                                                            <label class="form-label">Phone Number</label>
                                                                            <input type="text" name="users[0][phone]" class="form-control" placeholder="+1 234 567 8900">
                                                                        </div>
                                                                        <small class="text-muted">Optional contact number</small>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="input-group input-group-outline mb-3 is-focused is-filled">
                                                                            <select name="users[0][role]" class="form-control" required>
                                                                                <option value="">Choose Role</option>
                                                                                <option value="admin">👑 Admin</option>
                                                                                <option value="manager">👨‍💼 Manager</option>
                                                                                <option value="user">👤 User</option>
                                                                                <option value="viewer">👁️ Viewer</option>
                                                                            </select>
                                                                        </div>
                                                                        <small class="text-muted">Select user's access level</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center mt-5" id="no-users-message" style="display: none;">
                                                    <div class="py-5">
                                                        <div class="icon icon-lg icon-shape bg-gradient-secondary shadow mx-auto mb-3">
                                                            <i class="material-icons text-white" style="font-size: 32px;">people</i>
                                                        </div>
                                                        <h5 class="text-muted mt-3">No Users Added</h5>
                                                        <p class="text-muted">Start by adding users who will access this application</p>
                                                        <button type="button" class="btn btn-primary" onclick="addUser()">
                                                            <i class="material-icons me-1">add</i> Add First User
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Roles Tab -->
                                    <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="text-primary mb-1">Roles Management</h5>
                                                        <p class="text-sm text-muted mb-0">Define different access levels for your application</p>
                                                    </div>
                                                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center" onclick="addRole()">
                                                        <i class="material-icons me-1" style="font-size: 16px;">add</i>
                                                        Add Role
                                                    </button>
                                                </div>
                                                
                                                <div id="roles-container">
                                                    <!-- Default admin role -->
                                                    <div class="role-row mb-4" data-role-index="0">
                                                        <div class="card border-primary border">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="icon icon-sm icon-shape bg-gradient-primary shadow text-center me-3">
                                                                            <i class="material-icons opacity-10 text-white">admin_panel_settings</i>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0 text-dark">Administrator Role</h6>
                                                                            <small class="text-primary">System Default</small>
                                                                        </div>
                                                                    </div>
                                                                    <span class="badge bg-gradient-primary">Required</span>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="input-group input-group-outline mb-3 is-filled">
                                                                            <label class="form-label">Role Name *</label>
                                                                            <input type="text" name="roles[0][name]" class="form-control" value="admin" required readonly>
                                                                        </div>
                                                                        <small class="text-muted">System administrator role (cannot be modified)</small>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="input-group input-group-outline mb-3 is-filled">
                                                                            <label class="form-label">Description</label>
                                                                            <input type="text" name="roles[0][description]" class="form-control" value="Full system access and management capabilities">
                                                                        </div>
                                                                        <small class="text-muted">Role description and purpose</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Default user role -->
                                                    <div class="role-row mb-4" data-role-index="1">
                                                        <div class="card border-info border">
                                                            <div class="card-body p-4">
                                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="icon icon-sm icon-shape bg-gradient-info shadow text-center me-3">
                                                                            <i class="material-icons opacity-10 text-white">person</i>
                                                                        </div>
                                                                        <div>
                                                                            <h6 class="mb-0 text-dark">Standard User Role</h6>
                                                                            <small class="text-info">System Default</small>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center" onclick="removeRole(1)">
                                                                        <i class="material-icons me-1" style="font-size: 16px;">delete</i>
                                                                        Remove
                                                                    </button>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="input-group input-group-outline mb-3 is-filled">
                                                                            <label class="form-label">Role Name *</label>
                                                                            <input type="text" name="roles[1][name]" class="form-control" value="user" required>
                                                                        </div>
                                                                        <small class="text-muted">Unique identifier for this role</small>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="input-group input-group-outline mb-3 is-filled">
                                                                            <label class="form-label">Description</label>
                                                                            <input type="text" name="roles[1][description]" class="form-control" value="Standard user with basic access rights">
                                                                        </div>
                                                                        <small class="text-muted">Role description and purpose</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="text-center mt-5" id="no-roles-message" style="display: none;">
                                                    <div class="py-5">
                                                        <div class="icon icon-lg icon-shape bg-gradient-secondary shadow mx-auto mb-3">
                                                            <i class="material-icons text-white" style="font-size: 32px;">group</i>
                                                        </div>
                                                        <h5 class="text-muted mt-3">No Custom Roles</h5>
                                                        <p class="text-muted">Create custom roles to define specific access levels</p>
                                                        <button type="button" class="btn btn-primary" onclick="addRole()">
                                                            <i class="material-icons me-1">add</i> Create Custom Role
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Permissions Tab -->
                                    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="mb-4">
                                                    <h5 class="text-primary mb-1">Permissions Management</h5>
                                                    <p class="text-sm text-muted mb-0">Configure what each role can do in your application</p>
                                                </div>
                                                
                                                
                                                
                                                <div id="permissions-container">
                                                    <div class="card border-0 shadow-sm">
                                                        <div class="card-header bg-gradient-primary">
                                                            <div class="d-flex align-items-center">
                                                                <i class="material-icons text-white me-2">security</i>
                                                                <h6 class="text-white mb-0">Role Permissions Matrix</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive">
                                                                <table class="table table-hover mb-0">
                                                                    <thead class="bg-gray-100">
                                                                        <tr>
                                                                            <th class="border-0 ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="material-icons text-secondary me-2">build</i>
                                                                                    <span class="text-dark font-weight-bold">Permission</span>
                                                                                </div>
                                                                            </th>
                                                                            <th class="text-center border-0">
                                                                                <div class="d-flex flex-column align-items-center">
                                                                                    <div class="icon icon-xs icon-shape bg-gradient-primary shadow mb-1">
                                                                                        <i class="material-icons opacity-10 text-white" style="font-size: 12px;">admin_panel_settings</i>
                                                                                    </div>
                                                                                    <span class="text-dark font-weight-bold text-xs">Admin</span>
                                                                                </div>
                                                                            </th>
                                                                            <th class="text-center border-0">
                                                                                <div class="d-flex flex-column align-items-center">
                                                                                    <div class="icon icon-xs icon-shape bg-gradient-info shadow mb-1">
                                                                                        <i class="material-icons opacity-10 text-white" style="font-size: 12px;">person</i>
                                                                                    </div>
                                                                                    <span class="text-dark font-weight-bold text-xs">User</span>
                                                                                </div>
                                                                            </th>
                                                                            <th class="text-center border-0">
                                                                                <div class="d-flex flex-column align-items-center">
                                                                                    <div class="icon icon-xs icon-shape bg-gradient-warning shadow mb-1">
                                                                                        <i class="material-icons opacity-10 text-white" style="font-size: 12px;">supervisor_account</i>
                                                                                    </div>
                                                                                    <span class="text-dark font-weight-bold text-xs">Manager</span>
                                                                                </div>
                                                                            </th>
                                                                            <th class="text-center border-0">
                                                                                <div class="d-flex flex-column align-items-center">
                                                                                    <div class="icon icon-xs icon-shape bg-gradient-secondary shadow mb-1">
                                                                                        <i class="material-icons opacity-10 text-white" style="font-size: 12px;">visibility</i>
                                                                                    </div>
                                                                                    <span class="text-dark font-weight-bold text-xs">Viewer</span>
                                                                                </div>
                                                                            </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="permissions-table-body">
                                                                        <tr class="border-bottom">
                                                                            <td class="ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="material-icons text-success me-2">add_circle</i>
                                                                                    <div>
                                                                                        <span class="text-dark font-weight-bold">Create Records</span>
                                                                                        <br><small class="text-muted">Add new data to the system</small>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[create][admin]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[create][user]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[create][manager]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[create][viewer]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="border-bottom">
                                                                            <td class="ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="material-icons text-info me-2">visibility</i>
                                                                                    <div>
                                                                                        <span class="text-dark font-weight-bold">Read Records</span>
                                                                                        <br><small class="text-muted">View and access existing data</small>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[read][admin]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[read][user]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[read][manager]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[read][viewer]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="border-bottom">
                                                                            <td class="ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="material-icons text-warning me-2">edit</i>
                                                                                    <div>
                                                                                        <span class="text-dark font-weight-bold">Update Records</span>
                                                                                        <br><small class="text-muted">Modify existing data entries</small>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[update][admin]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[update][user]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[update][manager]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[update][viewer]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="ps-4">
                                                                                <div class="d-flex align-items-center">
                                                                                    <i class="material-icons text-danger me-2">delete</i>
                                                                                    <div>
                                                                                        <span class="text-dark font-weight-bold">Delete Records</span>
                                                                                        <br><small class="text-muted">Remove data from the system</small>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[delete][admin]" value="1" checked onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-success fw-bold ms-2">✓ Granted</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[delete][user]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[delete][manager]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                                    <input class="form-check-input" type="checkbox" name="permissions[delete][viewer]" value="1" onchange="updateCheckboxLabel(this)">
                                                                                    <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="card border-0 shadow-sm mt-4">
                                                        <div class="card-header bg-gradient-info">
                                                            <div class="d-flex align-items-center">
                                                                <i class="material-icons text-white me-2">storage</i>
                                                                <h6 class="text-white mb-0">Database Schema Permissions</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div id="schema-permissions" class="text-center py-4">
                                                                <div class="icon icon-lg icon-shape bg-gradient-secondary shadow mx-auto mb-3">
                                                                    <i class="material-icons text-white" style="font-size: 32px;">link_off</i>
                                                                </div>
                                                                <h6 class="text-muted">Database Not Connected</h6>
                                                                <p class="text-sm text-muted mb-3">Table-specific permissions will appear here after establishing database connection</p>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <i class="material-icons text-primary me-2">lightbulb</i>
                                                                    <small class="text-primary">Complete the "App Details" tab to connect your database</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Form Actions -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <hr class="horizontal dark">
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('apps.index') }}" class="btn btn-light me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="material-icons text-sm">save</i> Create App
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle database type change to set default ports
        const databaseTypeSelect = document.querySelector('select[name="database_type"]');
        const portInput = document.querySelector('input[name="port"]');
        
        if (databaseTypeSelect && portInput) {
            databaseTypeSelect.addEventListener('change', function() {
                const defaultPorts = {
                    'mysql': '3306',
                    'postgresql': '5432',
                    'sqlite': '',
                    'sqlserver': '1433',
                    'oracle': '1521'
                };
                
                if (defaultPorts[this.value] !== undefined) {
                    portInput.value = defaultPorts[this.value];
                    if (this.value === 'sqlite') {
                        portInput.disabled = true;
                    } else {
                        portInput.disabled = false;
                    }
                }
            });
        }
    });

    let userIndex = 1;
    let roleIndex = 2; // Starting at 2 since we have 2 default roles

    // Function to update checkbox labels dynamically
    function updateCheckboxLabel(checkbox) {
        const label = checkbox.nextElementSibling;
        if (checkbox.checked) {
            label.textContent = '✓ Granted';
            label.className = 'form-check-label text-success fw-bold ms-2';
        } else {
            label.textContent = '— Denied';
            label.className = 'form-check-label text-muted fw-bold ms-2';
        }
    }

    function addUser() {
        const container = document.getElementById('users-container');
        const noUsersMessage = document.getElementById('no-users-message');
        
        const userHtml = `
            <div class="user-row mb-4" data-user-index="${userIndex}">
                <div class="card border">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-sm icon-shape bg-gradient-info shadow text-center me-3">
                                    <i class="material-icons opacity-10 text-white">person</i>
                                </div>
                                <h6 class="mb-0 text-dark">User #${userIndex + 1}</h6>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center" onclick="removeUser(${userIndex})">
                                <i class="material-icons me-1" style="font-size: 16px;">delete</i>
                                Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="users[${userIndex}][name]" class="form-control" required>
                                </div>
                                <small class="text-muted">Enter the user's full name</small>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-outline mb-3 is-focused is-filled">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="users[${userIndex}][phone]" class="form-control" placeholder="+1 234 567 8900">
                                </div>
                                <small class="text-muted">Optional contact number</small>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-outline mb-3 is-focused is-filled">
                                    <select name="users[${userIndex}][role]" class="form-control" required>
                                        <option value="">Choose Role</option>
                                        <option value="admin">👑 Admin</option>
                                        <option value="manager">👨‍💼 Manager</option>
                                        <option value="user">👤 User</option>
                                        <option value="viewer">👁️ Viewer</option>
                                    </select>
                                </div>
                                <small class="text-muted">Select user's access level</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', userHtml);
        userIndex++;
        
        // Show delete buttons for all users if more than one
        updateDeleteButtons();
        
        // Hide no users message
        noUsersMessage.style.display = 'none';
    }

    function removeUser(index) {
        const userRow = document.querySelector(`[data-user-index="${index}"]`);
        if (userRow) {
            userRow.remove();
            
            // Update user numbers and delete button visibility
            updateUserNumbers();
            updateDeleteButtons();
            
            // Show no users message if no users left
            const container = document.getElementById('users-container');
            const noUsersMessage = document.getElementById('no-users-message');
            if (container.children.length === 0) {
                noUsersMessage.style.display = 'block';
            }
        }
    }

    function updateUserNumbers() {
        const userRows = document.querySelectorAll('.user-row');
        userRows.forEach((row, index) => {
            const title = row.querySelector('h6');
            if (title) {
                title.textContent = `User #${index + 1}`;
            }
        });
    }

    function updateDeleteButtons() {
        const userRows = document.querySelectorAll('.user-row');
        const deleteButtons = document.querySelectorAll('.user-row .btn-outline-danger');
        
        if (userRows.length <= 1) {
            // Hide delete buttons if only one user
            deleteButtons.forEach(btn => btn.style.display = 'none');
        } else {
            // Show delete buttons if more than one user
            deleteButtons.forEach(btn => btn.style.display = 'inline-block');
        }
    }

    // Role Management Functions
    function addRole() {
        const container = document.getElementById('roles-container');
        const noRolesMessage = document.getElementById('no-roles-message');
        
        const roleHtml = `
            <div class="role-row mb-4" data-role-index="${roleIndex}">
                <div class="card border-success border">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-sm icon-shape bg-gradient-success shadow text-center me-3">
                                    <i class="material-icons opacity-10 text-white">group_add</i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-dark">Custom Role #${roleIndex - 1}</h6>
                                    <small class="text-success">Custom Role</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center" onclick="removeRole(${roleIndex})">
                                <i class="material-icons me-1" style="font-size: 16px;">delete</i>
                                Remove
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Role Name *</label>
                                    <input type="text" name="roles[${roleIndex}][name]" class="form-control" required>
                                </div>
                                <small class="text-muted">Unique identifier for this role</small>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Description</label>
                                    <input type="text" name="roles[${roleIndex}][description]" class="form-control">
                                </div>
                                <small class="text-muted">Role description and purpose</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', roleHtml);
        
        // Add new column to permissions table
        addPermissionColumn(roleIndex);
        
        roleIndex++;
        
        // Show delete buttons for all roles if more than one
        updateRoleDeleteButtons();
        
        // Hide no roles message
        noRolesMessage.style.display = 'none';
    }

    function removeRole(index) {
        const roleRow = document.querySelector(`[data-role-index="${index}"]`);
        if (roleRow) {
            const roleName = roleRow.querySelector('input[name*="[name]"]').value;
            
            roleRow.remove();
            
            // Remove column from permissions table
            removePermissionColumn(roleName);
            
            // Update role numbers and delete button visibility
            updateRoleNumbers();
            updateRoleDeleteButtons();
            
            // Show no roles message if no roles left
            const container = document.getElementById('roles-container');
            const noRolesMessage = document.getElementById('no-roles-message');
            if (container.children.length === 0) {
                noRolesMessage.style.display = 'block';
            }
        }
    }

    function updateRoleNumbers() {
        const roleRows = document.querySelectorAll('.role-row');
        roleRows.forEach((row, index) => {
            const title = row.querySelector('h6');
            if (title) {
                title.textContent = `Role #${index + 1}`;
            }
        });
    }

    function updateRoleDeleteButtons() {
        const roleRows = document.querySelectorAll('.role-row');
        const deleteButtons = document.querySelectorAll('.role-row .btn-outline-danger');
        
        if (roleRows.length <= 1) {
            // Hide delete buttons if only one role
            deleteButtons.forEach(btn => btn.style.display = 'none');
        } else {
            // Show delete buttons if more than one role
            deleteButtons.forEach(btn => btn.style.display = 'inline-block');
        }
    }

    function addPermissionColumn(roleIndex) {
        const table = document.querySelector('#permissions-table-body');
        if (table) {
            const rows = table.querySelectorAll('tr');
            const headerRow = document.querySelector('#permissions-container thead tr');
            
            // Add header
            const newHeader = document.createElement('th');
            newHeader.className = 'text-center';
            newHeader.textContent = 'Custom Role';
            headerRow.appendChild(newHeader);
            
            // Add cells to each row
            rows.forEach((row, index) => {
                const newCell = document.createElement('td');
                newCell.className = 'text-center';
                
                const permissions = ['create', 'read', 'update', 'delete'];
                const permission = permissions[index];
                
                newCell.innerHTML = `
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" name="permissions[${permission}][role_${roleIndex}]" value="1" onchange="updateCheckboxLabel(this)">
                        <label class="form-check-label text-muted fw-bold ms-2">— Denied</label>
                    </div>
                `;
                row.appendChild(newCell);
            });
        }
    }

    function removePermissionColumn(roleName) {
        // This function would remove the column from permissions table
        // Implementation depends on how you want to handle dynamic role removal
}
</script>
