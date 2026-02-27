<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $colocations = Auth::user()->colocations()->with('owner')->latest()->get();
        return view('dashboard', compact('colocations'));
    }
}
