<?php

namespace App\Http\Controllers;

use App\Models\Plan;  // Add Plan model
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function pricing(Request $request)
    {
        // Fetch all plans from the database
        $plans = Plan::all();

        // Pass plans to the view
        return view('pricing', compact('plans'));
    }
}
