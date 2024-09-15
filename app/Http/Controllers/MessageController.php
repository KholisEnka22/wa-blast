<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $rules =[
            'img' => 'required|mimes:jpeg,png,jpg,gif,svg|max:5000',
            'message' => 'required'
        ];
        $message = [
            'img.required' => 'Image tidak boleh kosong',  
            'img.mimes' => 'File harus bertipe jpeg,png,jpg,gif,svg',
            'img.max' => 'Ukuran file terlalu besar',
            'message.required' => 'Message tidak boleh kosong'
        ];

        $this->validate($request, $rules, $message);

        //uplod img
        $file = $request->file('img');
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extension;
        $file->move(public_path('img'), $filename);

        Message::create([
            'img' => $filename,
            'message' => $request->message
        ]);
        return redirect()->route('inbox.index')->with('message.type', 'success')
                                            ->with('message.content', 'Data berhasil ditambah.');

    }
}
