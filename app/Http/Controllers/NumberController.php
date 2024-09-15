<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NumberController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Number',
            'number' => Number::all(),
            'message' => Message::all()
        ];

        return view('data.number', $data);
    }
    public function store(Request $request)
    {
        // Validasi input 'numbers' dan 'message_id'
        $validatedData = $request->validate([
            'numbers' => 'required|string',  // Validasi untuk textarea 'numbers'
            'message_id' => 'required|exists:messages,id',  // Validasi untuk 'message_id'
        ]);

        // Pisahkan nomor berdasarkan newline atau koma
        $numbers = preg_split('/[\r\n,]+/', $request->input('numbers'));

        // Ambil 'message_id'
        $message = $request->input('message_id');

        // Loop nomor, simpan tiap nomor ke database dengan status default 'belum terkirim'
        foreach ($numbers as $number) {
            $number = trim($number);  // Trim setiap nomor

            // Simpan hanya jika nomor tidak kosong
            if (!empty($number) && !DB::table('numbers')->where('number', $number)->exists()) {
                Number::create([
                    'number' => $number,
                    'message_id' => $message,
                    'status' => 'belum terkirim',
                ]);
            }
        }

        // Redirect dengan pesan sukses
        return redirect()->back()->with('message.type', 'success')
                                            ->with('message.content', 'Data berhasil ditambah.');

    }

}
