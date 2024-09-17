<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function executeCurl()
    {
        $numbers = DB::table('numbers')
        ->join('messages', 'numbers.message_id', '=', 'messages.id')  // Join ke tabel messages
        ->where('numbers.status', 'belum terkirim')  // Kondisi 'belum terkirim'
        ->select('numbers.*','messages.img_url', 'messages.message as message_text')  // Pilih semua kolom dari numbers dan pesan dari messages
        ->get();
        
        foreach ($numbers as $item) {
            
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_PORT => '60000',
                CURLOPT_URL => 'http://147.139.201.32:60000/send-image',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'to' => $item->number,
                    'text' =>  strip_tags($item->message_text),
                    'imageUrl' => 'https://sampah.cloudside.id/images/737068226678d131.png', // Pastikan `img_url` adalah field yang benar
                ]),
                CURLOPT_HTTPHEADER => ['Accept: */*', 'Content-Type: application/json', 'User-Agent: Thunder Client (https://www.thunderclient.com)'],
            ]);

            $response = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);

            if ($err) {
                // Log error atau tangani sesuai kebutuhan
                Log::error('cURL Error #: ' . $err);
                DB::table('numbers')
                ->where('id',  $item->id)
                ->update(['status' => 'Number not registered']);
            } else {
                // Log atau tangani response sesuai kebutuhan
                Log::info('cURL Response: ' . $response);
                DB::table('numbers')
                ->where('id',  $item->id)
                ->update(['status' => 'terkirim']);


            }

        }

        return response()->json($numbers);
    }

}
