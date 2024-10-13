<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Message',
            'pesan' => Message::all()
        ];
        return view('data.message', $data);
    }

    public function store(Request $request)
    {
        // Validasi input
        $rules = [
            'img_url' => 'required|url',
            'message' => 'required'
        ];
        $messages = [
            'img_url.required' => 'Image Url tidak boleh kosong',
            'img_url.url' => 'Image Url harus berupa URL yang valid',
            'message.required' => 'Message tidak boleh kosong'
        ];

        $this->validate($request, $rules, $messages);

        // Simpan data ke dalam database
        Message::create([
            'img' => $request->img_url,
            'message' => $request->message
        ]);

        // Redirect ke halaman inbox dengan pesan sukses
        return redirect()->route('message.index')
            ->with('message.type', 'success')
            ->with('message.content', 'Data berhasil ditambah.');
    }

    public function destroy($id)
    {
        Message::findOrFail($id)->delete();
        return redirect()->back()
            ->with('message.type', 'success')
            ->with('message.content', 'Data berhasil dihapus.');

    }
}