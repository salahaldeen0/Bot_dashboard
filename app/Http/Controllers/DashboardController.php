<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\App;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalApps = App::count();
        $connectedApps = App::where('is_connected', true)->count();
        $appsWithSyncedSchema = App::where('has_synced_schema', true)->count();
        
        // Get recent apps (last 5)
        $recentApps = App::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get apps created per month for the last 12 months
        $monthlyAppsData = App::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();
        
        // Format chart data for last 12 months
        $chartLabels = [];
        $appsChartData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M');
            $chartLabels[] = $monthName;
            
            // Find matching data
            $appCount = $monthlyAppsData->first(function($item) use ($date) {
                return $item->month == $date->month && $item->year == $date->year;
            });
            
            $appsChartData[] = $appCount ? $appCount->count : 0;
        }
        
        // Calculate growth percentages
        $previousMonthApps = App::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $currentMonthApps = App::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $appsGrowth = $previousMonthApps > 0 
            ? round((($currentMonthApps - $previousMonthApps) / $previousMonthApps) * 100, 1) 
            : 0;
        
        return view('dashboard.index', compact(
            'totalApps',
            'connectedApps',
            'appsWithSyncedSchema',
            'recentApps',
            'chartLabels',
            'appsChartData',
            'appsGrowth'
        ));
    }
}
