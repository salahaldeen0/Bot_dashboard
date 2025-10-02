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
                                            <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#roles-permissions" role="tab" aria-controls="roles-permissions" aria-selected="false">
                                                <i class="material-icons text-lg position-relative">security</i>
                                                <span class="ms-1">Roles & Permissions</span>
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
                                                <div class="input-group input-group-outline mb-3 @error('host') is-focused is-filled @enderror">
                                                    <label class="form-label">Host *</label>
                                                    <input type="text" name="host" class="form-control @error('host') is-invalid @enderror" value="{{ old('host', 'localhost') }}" required>
                                                    @error('host')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3 @error('port') is-focused is-filled @enderror">
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
                                                <div class="text-center py-5">
                                                    <i class="material-icons text-muted" style="font-size: 64px;">people</i>
                                                    <h5 class="text-muted mt-3">User Management</h5>
                                                    <p class="text-muted">User management features will be implemented here.</p>
                                                    <p class="text-sm text-secondary">This section will allow you to manage application users, their profiles, and access levels.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Roles & Permissions Tab -->
                                    <div class="tab-pane fade" id="roles-permissions" role="tabpanel" aria-labelledby="roles-permissions-tab">
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="text-center py-5">
                                                    <i class="material-icons text-muted" style="font-size: 64px;">security</i>
                                                    <h5 class="text-muted mt-3">Roles & Permissions</h5>
                                                    <p class="text-muted">Roles and permissions management features will be implemented here.</p>
                                                    <p class="text-sm text-secondary">This section will allow you to create roles, assign permissions, and manage user access control.</p>
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
</script>
