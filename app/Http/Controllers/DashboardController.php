<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'title' => 'Dashboard',
            'jumlah_number_not_registered' => \App\Models\Number::whereStatus('number not registered')->count(),
            'pandding' => \App\Models\Number::whereStatus('belum terkirim')->count(),
        ]);
    }
}