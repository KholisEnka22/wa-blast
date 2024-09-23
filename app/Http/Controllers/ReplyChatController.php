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


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://147.139.201.32:60000/send-message');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'to' => $request->from,
            'text' => $request->pesan,
            'sessionId' => $request->session_id,
        ]));

        $headers = array();
        $headers[] = 'Accept: */*';
        $headers[] = 'User-Agent: Thunder Client (https://www.thunderclient.com)';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);

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
