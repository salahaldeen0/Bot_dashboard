<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='apps'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Edit App"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <form action="{{ route('apps.update', $app->id) }}" method="POST" id="appForm" data-app-id="{{ $app->id }}">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="text-white text-capitalize ps-3">Edit App: {{ $app->app_name }}</h6>
                                        <div class="me-3">
                                            <a href="{{ route('apps.index') }}" class="btn btn-outline-white btn-sm mb-0">
                                                <i class="material-icons text-sm">arrow_back</i> Back to Apps
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Progressive Onboarding Status -->
                                {{-- <div class="alert {{ $app->canShowRolesTab() ? 'alert-success' : 'alert-info' }} alert-dismissible fade show" role="alert">
                                    <div class="d-flex align-items-start">
                                        <i class="material-icons me-3" style="font-size: 24px;">{{ $app->canShowRolesTab() ? 'check_circle' : 'info' }}</i>
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading mb-2">Setup Progress</h6>
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="material-icons text-success me-2" style="font-size: 18px;">check_circle</i>
                                                <span class="text-sm"><strong>App Created</strong> - Basic details saved</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                @if($app->is_connected)
                                                    <i class="material-icons text-success me-2" style="font-size: 18px;">check_circle</i>
                                                    <span class="text-sm"><strong>Database Connected</strong> - Schema tab unlocked</span>
                                                @else
                                                    <i class="material-icons text-warning me-2" style="font-size: 18px;">radio_button_unchecked</i>
                                                    <span class="text-sm">Connect database to unlock Schema tab</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                @if($app->has_synced_schema)
                                                    <i class="material-icons text-success me-2" style="font-size: 18px;">check_circle</i>
                                                    <span class="text-sm"><strong>Schema Synced</strong> - Users tab unlocked</span>
                                                @else
                                                    <i class="material-icons {{ $app->is_connected ? 'text-warning' : 'text-muted' }} me-2" style="font-size: 18px;">radio_button_unchecked</i>
                                                    <span class="text-sm {{ $app->is_connected ? '' : 'text-muted' }}">Tables will auto-sync after connection</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if($app->users_count > 0)
                                                    <i class="material-icons text-success me-2" style="font-size: 18px;">check_circle</i>
                                                    <span class="text-sm"><strong>Users Added ({{ $app->users_count }})</strong> - Roles & Permissions tab unlocked</span>
                                                @else
                                                    <i class="material-icons {{ $app->has_synced_schema ? 'text-warning' : 'text-muted' }} me-2" style="font-size: 18px;">radio_button_unchecked</i>
                                                    <span class="text-sm {{ $app->has_synced_schema ? '' : 'text-muted' }}">Add users to unlock Roles & Permissions</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div> --}}

                                <!-- Navigation Tabs -->
                                <div class="nav-wrapper position-relative end-0">
                                    <ul class="nav nav-pills nav-fill p-1" role="tablist" id="appTabs">
                                        <li class="nav-item">
                                            <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#app-details" role="tab" aria-controls="app-details" aria-selected="true">
                                                <i class="material-icons text-lg position-relative">apps</i>
                                                <span class="ms-1">App Details</span>
                                            </a>
                                        </li>
                                        @if($app->canShowSchemaTab())
                                        <li class="nav-item" id="schema-tab-li">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#schema" role="tab" aria-controls="schema" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">account_tree</i>
                                                <span class="ms-1">Schema</span>
                                                @if($app->has_synced_schema)
                                                    <i class="material-icons text-success ms-1" style="font-size: 14px;">check_circle</i>
                                                @endif
                                            </a>
                                        </li>
                                        @endif
                                        @if($app->canShowUsersTab())
                                        <li class="nav-item" id="users-tab-li">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">people</i>
                                                <span class="ms-1">Users</span>
                                                @if($app->users_count > 0)
                                                    <span class="badge badge-sm bg-gradient-success ms-1">{{ $app->users_count }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        @endif
                                        @if($app->canShowRolesTab())
                                        <li class="nav-item" id="roles-tab-li">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#roles" role="tab" aria-controls="roles" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">group</i>
                                                <span class="ms-1">Roles</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" id="permissions-tab-li">
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#permissions" role="tab" aria-controls="permissions" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">security</i>
                                                <span class="ms-1">Permissions</span>
                                            </a>
                                        </li>
                                        @endif
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
                                                <div class="input-group input-group-outline mb-3 is-filled @error('app_name') is-focused @enderror">
                                                    <label class="form-label">App Name *</label>
                                                    <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror" value="{{ old('app_name', $app->app_name) }}" required>
                                                    @error('app_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @if($app->phone_number || old('phone_number')) is-filled @endif @error('phone_number') is-focused @enderror">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $app->phone_number) }}">
                                                    @error('phone_number')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="input-group input-group-outline mb-3 @if($app->description || old('description')) is-filled @endif @error('description') is-focused @enderror">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $app->description) }}</textarea>
                                                    @error('description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-12 d-flex justify-content-between align-items-center">
                                                <h6 class="text-primary mb-0">Database Connection</h6>
                                                @if($app->is_connected)
                                                    <span class="badge bg-success">Connected</span>
                                                @else
                                                    <span class="badge bg-warning">Not Connected</span>
                                                @endif
                                            </div>
                                            <div class="col-12"><hr class="horizontal dark"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @if($app->database_type) is-filled @endif @error('database_type') is-focused @enderror">
                                                    <select id="database_type" name="database_type" class="form-control @error('database_type') is-invalid @enderror">
                                                        <option value="">Select Database Type</option>
                                                        <option value="mysql" {{ old('database_type', $app->database_type) == 'mysql' ? 'selected' : '' }}>MySQL</option>
                                                        <option value="postgresql" {{ old('database_type', $app->database_type) == 'postgresql' ? 'selected' : '' }}>PostgreSQL</option>
                                                        <option value="sqlite" {{ old('database_type', $app->database_type) == 'sqlite' ? 'selected' : '' }}>SQLite</option>
                                                        <option value="sqlserver" {{ old('database_type', $app->database_type) == 'sqlserver' ? 'selected' : '' }}>SQL Server</option>
                                                        <option value="oracle" {{ old('database_type', $app->database_type) == 'oracle' ? 'selected' : '' }}>Oracle</option>
                                                    </select>
                                                    @error('database_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @if($app->database_name) is-filled @endif @error('database_name') is-focused @enderror">
                                                    <label class="form-label">Database Name</label>
                                                    <input type="text" id="database_name" name="database_name" class="form-control @error('database_name') is-invalid @enderror" value="{{ old('database_name', $app->database_name) }}">
                                                    @error('database_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @if($app->host) is-filled @endif @error('host') is-focused @enderror">
                                                    <label class="form-label">Host</label>
                                                    <input type="text" id="host" name="host" class="form-control @error('host') is-invalid @enderror" value="{{ old('host', $app->host) }}">
                                                    @error('host')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @if($app->port) is-filled @endif @error('port') is-focused @enderror">
                                                    <label class="form-label">Port</label>
                                                    <input type="number" id="port" name="port" class="form-control @error('port') is-invalid @enderror" value="{{ old('port', $app->port) }}" min="1" max="65535">
                                                    @error('port')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @if($app->username) is-filled @endif @error('username') is-focused @enderror">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" id="db_username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $app->username) }}">
                                                    @error('username')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('password') is-focused is-filled @enderror">
                                                    <label class="form-label">Password (optional - leave empty to keep current)</label>
                                                    <input type="password" id="db_password" name="password" class="form-control @error('password') is-invalid @enderror">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="button" class="btn btn-info" onclick="connectDatabase()" id="connectBtn">
                                                    <i class="material-icons me-1" style="font-size: 16px;">cable</i>
                                                    {{ $app->is_connected ? 'Reconnect Database' : 'Connect Database' }}
                                                </button>
                                                <span id="connection-status" class="ms-3"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Schema Tab -->
                                    <div class="tab-pane fade" id="schema" role="tabpanel" aria-labelledby="schema-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="text-primary mb-1">Database Schema</h5>
                                                        <p class="text-sm text-muted mb-0">Manage tables, keywords, and active status</p>
                                                    </div>
                                                    <div>
                                                        <input type="text" id="tableSearch" class="form-control form-control-sm me-2" placeholder="Search tables..." style="display: inline-block; width: 200px;">
                                                        <button type="button" class="btn btn-info btn-sm" onclick="syncTables()" id="syncTablesBtn" data-has-synced-schema="{{ $app->has_synced_schema ? 'true' : 'false' }}">
                                                            <i class="material-icons me-1" style="font-size: 16px;">sync</i>
                                                            Sync Tables
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div id="schema-loading" class="text-center py-5" style="display: none;">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-3 text-muted">Loading tables...</p>
                                                </div>

                                                <div id="schema-tables-container">
                                                    <div class="card">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-items-center mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Table Name</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keywords</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Active</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="schema-tables-body">
                                                                    <!-- Tables will be loaded here via AJAX -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Pagination -->
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <div>
                                                            <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;" onchange="loadTables()">
                                                                <option value="5">5 per page</option>
                                                                <option value="10" selected>10 per page</option>
                                                                <option value="25">25 per page</option>
                                                                <option value="50">50 per page</option>
                                                            </select>
                                                        </div>
                                                        <nav id="schema-pagination">
                                                            <!-- Pagination will be loaded here -->
                                                        </nav>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Users Tab -->
                                    @if($app->canShowUsersTab())
                                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="text-primary mb-1">Users Management</h5>
                                                        <p class="text-sm text-muted mb-0">Manage application users and their information</p>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-sm" id="addUserBtn">
                                                            <i class="material-icons me-1" style="font-size: 16px;">add</i>
                                                            Add User
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div id="users-loading" class="text-center py-5" style="display: none;">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-3 text-muted">Loading users...</p>
                                                </div>

                                                <div id="users-container">
                                                    <div class="card">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-items-center mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Phone</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created At</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Role</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="users-table-body">
                                                                    <!-- Users will be loaded here via AJAX -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Roles Tab -->
                                    @if($app->canShowRolesTab())
                                    <div class="tab-pane fade" id="roles" role="tabpanel" aria-labelledby="roles-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="text-primary mb-1">Roles Management</h5>
                                                        <p class="text-sm text-muted mb-0">Create and manage roles for your application</p>
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-sm" id="addRoleBtn">
                                                            <i class="material-icons me-1" style="font-size: 16px;">add</i>
                                                            Add Role
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div id="roles-loading" class="text-center py-5" style="display: none;">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-3 text-muted">Loading roles...</p>
                                                </div>

                                                <div id="roles-container">
                                                    <div class="card">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-items-center mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role Name</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created At</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="roles-table-body">
                                                                    <!-- Roles will be loaded here via AJAX -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Permissions Tab -->
                                    <div class="tab-pane fade" id="permissions" role="tabpanel" aria-labelledby="permissions-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div>
                                                        <h5 class="text-primary mb-1">Permissions Management</h5>
                                                        <p class="text-sm text-muted mb-0">Configure table access permissions for each role</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <label for="roleSelectPermissions" class="me-2 mb-0 text-md">Select Role:</label>
                                                        <select class="form-select form-select-sm" id="roleSelectPermissions" style="width: 400px;">
                                                            <option value="">Choose a role...</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div id="permissions-no-role" class="text-center py-5">
                                                    <i class="material-icons text-muted" style="font-size: 48px;">security</i>
                                                    <p class="text-muted mt-3">Select a role to configure its permissions</p>
                                                </div>
                                                
                                                <div id="permissions-loading" class="text-center py-5" style="display: none;">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-3 text-muted">Loading permissions...</p>
                                                </div>

                                                <div id="permissions-container" style="display: none;">
                                                    <div class="alert alert-info">
                                                        <i class="material-icons me-2" style="font-size: 18px; vertical-align: middle;">info</i>
                                                        <span id="selectedRoleName" class="fw-bold"></span> - Check the actions this role can perform on each table
                                                    </div>
                                                    
                                                    <div class="card">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover align-items-center mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Table Name</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Create</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Read</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Update</th>
                                                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Delete</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="permissions-table-body">
                                                                    <!-- Permissions will be loaded here via AJAX -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-end mt-3">
                                                        <button type="button" class="btn btn-primary" id="savePermissionsBtn">
                                                            <i class="material-icons me-1" style="font-size: 16px;">save</i>
                                                            Save Permissions
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Form Actions -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <hr class="horizontal dark">
                                        <div class="d-flex justify-content-end">
                                            <a href="{{ route('apps.index') }}" class="btn btn-light me-2">Cancel</a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="material-icons text-sm">save</i> Update App
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
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <input type="hidden" id="editUserId" value="">
                        <div class="mb-3">
                            <label for="newUserName" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newUserName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="newUserPhone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="newUserPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="newUserRole" class="form-label">Role</label>
                            <select class="form-select" id="newUserRole" name="role">
                                <option value="">Select Role (optional)</option>
                                <!-- Roles will be populated dynamically -->
                            </select>
                            <small class="text-muted">Assign a role to this user</small>
                        </div>
                    </form>
                    <div id="addUserAlert" style="display:none;" class="alert alert-info mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitAddUserBtn">Create User</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRoleForm">
                        <input type="hidden" id="editRoleId" value="">
                        <div class="mb-3">
                            <label for="newRoleName" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="newRoleName" name="role_name" required placeholder="e.g., Admin, User, Manager">
                        </div>
                        <div class="mb-3">
                            <label for="newRoleDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="newRoleDescription" name="description" rows="3" placeholder="Describe the role's purpose and permissions"></textarea>
                        </div>
                    </form>
                    <div id="addRoleAlert" style="display:none;" class="alert alert-info mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitAddRoleBtn">Create Role</button>
                </div>
            </div>
        </div>
    </div>

    <x-plugins></x-plugins>
    <script src="{{ asset('assets/js/app-edit.js') }}"></script>
</x-layout>
