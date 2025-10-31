<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DashboardExport implements FromView
{
    protected $stats;
    protected $filters;

    public function __construct(array $stats, array $filters)
    {
        $this->stats = $stats;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('exports.dashboard', [
            'stats' => $this->stats,
            'filters' => $this->filters
        ]);
    }
}
