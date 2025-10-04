<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    private string $source;

    public function __construct()
    {
        $this->source    = "Dashboard/Pages/";
        $this->middleware('auth');
    }

    public function dashboard()
    {
        return Inertia::render("{$this->source}Index", [
            'data' => null,
        ]);
    }
}
