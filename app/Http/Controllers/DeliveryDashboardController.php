<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryDashboardController extends Controller
{
    /**
     * Display the delivery dashboard
     */
    public function index()
    {
        return view('delivery.dashboard');
    }
}
