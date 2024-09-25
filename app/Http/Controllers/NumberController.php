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
            'number' => Number::paginate(10),
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
            if (!empty($number)) {
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
            ->select('numbers.*', 'messages.img', 'messages.message as message_text')  // Pilih semua kolom dari numbers dan pesan dari messages
            ->get();

        foreach ($numbers as $item) {

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'http://147.139.201.32:60000/send-image');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'to' => $item->number,
                'text' => $item->message_text,
                'imageUrl' => $item->img,
            ]));

            $headers = array();
            $headers[] = 'Accept: */*';
            $headers[] = 'User-Agent: Thunder Client (https://www.thunderclient.com)';
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            $err = curl_errno($ch);
            curl_close($ch);

            // echo "Result: " . $result . "\n"; // Print result
            // jika pengiriman gagal maka dianggap nomor tidak valid 

            $response = json_decode($result, true);
            if (isset($response['error']) && $response['error'] === 'Failed to send message') {
                // Log error atau tangani sesuai kebutuhan
                Log::error('cURL Error #: ' . $response['error']);
                DB::table('numbers')
                    ->where('id',  $item->id)
                    ->update(['status' => 'Number not registered']);
                // jika repon dari api server "Message sent with session" maka nomro valid dan sudah terkirim
            } else if (isset($response['message']) && preg_match('/Message sent with session/', $response['message'])) {
                // Log atau tangani response sesuai kebutuhan
                Log::info('cURL Response: ' . $result);
                DB::table('numbers')
                    ->where('id',  $item->id)
                    ->update(['status' => 'belum terkirim']);
                // jika respon ga ada maka session habis dan menjadi status belum di kirim
            } else {
                // Log atau tangani response sesuai kebutuhan
                Log::info('cURL Response: ' . $result);
                DB::table('numbers')
                    ->where('id',  $item->id)
                    ->update(['status' => 'terkirim']);
            }
        }

        return response()->json($numbers);
    }
}
