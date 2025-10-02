<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='apps'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="App Management"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-white text-capitalize ps-3">Apps Management</h6>
                                    <div class="me-3">
                                        <a href="{{ route('apps.create') }}" class="btn btn-outline-white btn-sm mb-0">
                                            <i class="material-icons text-sm">add</i> Add New App
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible mx-3" role="alert">
                                    <span class="text-white">{{ session('success') }}</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">App Name</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Database</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
                                            <th class="text-secondary opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($apps as $app)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $app->app_name }}</h6>
                                                            @if($app->phone_number)
                                                                <p class="text-xs text-secondary mb-0">{{ $app->phone_number }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ Str::limit($app->description, 50) }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm bg-gradient-success">{{ $app->database_type }}</span>
                                                    <p class="text-xs text-secondary mb-0">{{ $app->database_name }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $app->created_at->format('M d, Y') }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="dropdown">
                                                        <button class="btn btn-link text-secondary mb-0" type="button" id="dropdownMenuButton{{ $app->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-v text-xs"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $app->id }}">
                                                            <li><a class="dropdown-item" href="{{ route('apps.show', $app->id) }}">
                                                                <i class="material-icons text-sm me-2">visibility</i> View
                                                            </a></li>
                                                            <li><a class="dropdown-item" href="{{ route('apps.edit', $app->id) }}">
                                                                <i class="material-icons text-sm me-2">edit</i> Edit
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('apps.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this app?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="material-icons text-sm me-2">delete</i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <i class="material-icons text-muted" style="font-size: 48px;">apps</i>
                                                        <h6 class="text-muted mt-2">No apps found</h6>
                                                        <p class="text-sm text-muted">Create your first app to get started</p>
                                                        <a href="{{ route('apps.create') }}" class="btn btn-primary btn-sm">
                                                            <i class="material-icons text-sm">add</i> Add New App
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout>
