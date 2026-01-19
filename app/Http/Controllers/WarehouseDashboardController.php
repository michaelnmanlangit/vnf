<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseDashboardController extends Controller
{
    /**
     * Display the warehouse dashboard
     */
    public function index()
    {
        return view('warehouse.dashboard');
    }
}
