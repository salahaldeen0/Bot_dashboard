<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">apps</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Apps</p>
                                <h4 class="mb-0">{{ $totalApps }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                @if($appsGrowth > 0)
                                    <span class="text-success text-sm font-weight-bolder">+{{ $appsGrowth }}% </span>
                                @elseif($appsGrowth < 0)
                                    <span class="text-danger text-sm font-weight-bolder">{{ $appsGrowth }}% </span>
                                @else
                                    <span class="text-secondary text-sm font-weight-bolder">{{ $appsGrowth }}% </span>
                                @endif
                                than last month
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">link</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Connected Apps</p>
                                <h4 class="mb-0">{{ $connectedApps }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-sm">
                                    {{ $totalApps > 0 ? round(($connectedApps / $totalApps) * 100) : 0 }}% of total apps
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div
                                class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">sync</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Synced Schema</p>
                                <h4 class="mb-0">{{ $appsWithSyncedSchema }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-sm">
                                    {{ $totalApps > 0 ? round(($appsWithSyncedSchema / $totalApps) * 100) : 0 }}% of total apps
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6>Apps Created</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-check text-info" aria-hidden="true"></i>
                                        <span class="font-weight-bold ms-1">Last 12 months</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="chart">
                                <canvas id="chart-apps" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Apps Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Recent Apps</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            @if($recentApps->count() > 0)
                                <div class="table-responsive p-0">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">App Name</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Users</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Created</th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentApps as $app)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div>
                                                                <div class="icon icon-shape icon-sm border-radius-md bg-gradient-primary shadow text-center me-2 d-flex align-items-center justify-content-center">
                                                                    <i class="material-icons opacity-10">apps</i>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">{{ $app->app_name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0">{{ Str::limit($app->description, 50) }}</p>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        @if($app->is_connected)
                                                            <span class="badge badge-sm bg-gradient-success">Connected</span>
                                                        @else
                                                            <span class="badge badge-sm bg-gradient-secondary">Not Connected</span>
                                                        @endif
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $app->users_count ?? 0 }}</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">{{ $app->created_at->diffForHumans() }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="{{ route('apps.show', $app->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View app">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3">
                                    <p class="text-sm mb-0">No apps created yet. <a href="{{ route('apps.create') }}">Create your first app</a></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
    </div>
    @push('js')
    <script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>
    <script>
        // Chart data from backend
        var chartLabels = @json($chartLabels);
        var appsChartData = @json($appsChartData);

        // Apps Created Chart
        var ctxApps = document.getElementById("chart-apps").getContext("2d");
        new Chart(ctxApps, {
            type: "bar",
            data: {
                labels: chartLabels,
                datasets: [{
                    label: "Apps Created",
                    tension: 0.4,
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false,
                    backgroundColor: "#5e72e4",
                    data: appsChartData,
                    maxBarThickness: 6
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Apps: ' + context.parsed.y;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            suggestedMin: 0,
                            beginAtZero: true,
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            }
                        },
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            }
                        }
                    },
                },
            },
        });
    </script>
    @endpush
</x-layout>
