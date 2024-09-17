<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $rules =[
            'img_url' => 'required',
            'message' => 'required'
        ];
        $message = [
            'img_url.required' => 'Image Url tidak boleh kosong',
            'message.required' => 'Message tidak boleh kosong'
        ];

        $this->validate($request, $rules, $message);

        Message::create([
            'img_url' => $request->img_url,
            'message' => $request->message
        ]);
        return redirect()->route('inbox.index')->with('message.type', 'success')
                                            ->with('message.content', 'Data berhasil ditambah.');

    }
}
