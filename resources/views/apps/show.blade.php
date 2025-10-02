<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='apps'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="View App"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-white text-capitalize ps-3">App Details: {{ $app->app_name }}</h6>
                                    <div class="me-3">
                                        <a href="{{ route('apps.edit', $app->id) }}" class="btn btn-outline-white btn-sm mb-0 me-2">
                                            <i class="material-icons text-sm">edit</i> Edit
                                        </a>
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
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">App Name</label>
                                                <p class="form-control-static h6">{{ $app->app_name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Phone Number</label>
                                                <p class="form-control-static">{{ $app->phone_number ?: 'Not provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Description</label>
                                                <p class="form-control-static">{{ $app->description ?: 'No description provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Created At</label>
                                                <p class="form-control-static">{{ $app->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Last Updated</label>
                                                <p class="form-control-static">{{ $app->updated_at->format('M d, Y H:i') }}</p>
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
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Database Type</label>
                                                <p class="form-control-static">
                                                    <span class="badge badge-lg bg-gradient-success">{{ strtoupper($app->database_type) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Database Name</label>
                                                <p class="form-control-static">{{ $app->database_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Host</label>
                                                <p class="form-control-static">{{ $app->host }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Port</label>
                                                <p class="form-control-static">{{ $app->port }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Username</label>
                                                <p class="form-control-static">{{ $app->username }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-secondary">Password</label>
                                                <p class="form-control-static">
                                                    <span class="text-muted">••••••••</span>
                                                    <small class="text-secondary ms-2">(Hidden for security)</small>
                                                </p>
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
                                                <p class="text-sm text-secondary">This section will display database schemas, tables, and relationships for <strong>{{ $app->app_name }}</strong>.</p>
                                                <div class="mt-4">
                                                    <div class="alert alert-info">
                                                        <strong>Connection Details:</strong><br>
                                                        Database: {{ $app->database_type }} - {{ $app->database_name }}<br>
                                                        Host: {{ $app->host }}:{{ $app->port }}
                                                    </div>
                                                </div>
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
                                                <p class="text-sm text-secondary">This section will display and manage users for <strong>{{ $app->app_name }}</strong>.</p>
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
                                                <p class="text-sm text-secondary">This section will display and manage roles and permissions for <strong>{{ $app->app_name }}</strong>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout>
