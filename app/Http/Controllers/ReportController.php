<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Number;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Report',
            'number' => Number::all(),
            'message' => Message::all()
        ];

        return view('data.report', $data);
    }
}
