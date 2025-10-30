<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private string $source;
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->source = "Dashboard/";
        $this->dashboardService = $dashboardService;

        $this->middleware('auth');
        $this->middleware('role:Admin')->only(['exportExcel', 'exportPdf']);
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $dashboardData = $this->dashboardService->getDataForUser($user, $startDate, $endDate);
        if (is_null($dashboardData)) {
            Auth::logout();
            return redirect('/');
        }
        return Inertia::render($dashboardData['view'], $dashboardData['props']);
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        return $this->dashboardService->exportToExcel($startDate, $endDate);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        return $this->dashboardService->exportToPdf($startDate, $endDate);
    }
}
