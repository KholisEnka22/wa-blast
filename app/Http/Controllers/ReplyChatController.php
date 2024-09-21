<?php

namespace App\Http\Controllers;

use App\Models\ReplyChat;
use Illuminate\Http\Request;

class ReplyChatController extends Controller
{
    public function detail($from)
    {
        // Find the chat by session_id
        $chat = ReplyChat::where('from', $from)->get();

        // Check if chat data exists
        if ($chat->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        // Return chat data as JSON response
        return response()->json([
            'message' => 'Data berhasil ditemukan.',
            'data' => $chat
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'session_id' => 'required',
            'pesan' => 'required',
            'from' => 'required'
        ];

        $message = [
            'session_id.required' => 'Session ID tidak boleh kosong',
            'pesan.required' => 'Pesan tidak boleh kosong',
            'from.required' => 'From tidak boleh kosong'
        ];

        $this->validate($request, $rules, $message);

        ReplyChat::create([
            'session_id' => $request->session_id,
            'from' => $request->from,
            'pesan' => $request->pesan
        ]);

        return response()->json([
            'message' => 'Data berhasil ditambah.'
        ]);

        // return redirect()->back()->with('message.type', 'success')
        //     ->with('message.content', 'Data berhasil ditambah.');

    }
}