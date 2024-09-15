<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Inbox',
        ];

        return view('data.inbox', $data);
    }
}
