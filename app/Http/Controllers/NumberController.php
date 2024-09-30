<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Number;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NumberController extends Controller
{
    public function index()
    {
        $messages = Message::all();
        foreach ($messages as $message) {
            // Menghapus tag HTML dan membatasi panjang pesan
            $message->short_message = Str::limit(strip_tags($message->message), 60);
        }

        // Simpan ke array data yang akan dikirim ke view
        $data = [
            'title' => 'Number',
            'number' => Number::paginate(10), // Pagination untuk numbers
            'message' => $messages, // Short message disimpan
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

    // public function executeCurl()
    // {
    //     $numbers = DB::table('numbers')
    //         ->join('messages', 'numbers.message_id', '=', 'messages.id')  // Join ke tabel messages
    //         ->where('numbers.status', 'belum terkirim')  // Kondisi 'belum terkirim'
    //         ->select('numbers.*', 'messages.img', 'messages.message as message_text')  // Pilih semua kolom dari numbers dan pesan dari messages
    //         ->get();

    //     foreach ($numbers as $item) {

    //         $ch = curl_init();

    //         curl_setopt($ch, CURLOPT_URL, 'http://147.139.201.32:60000/send-image');
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch, CURLOPT_POST, 1);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    //             'to' => $item->number,
    //             'text' => $item->message_text,
    //             'imageUrl' => $item->img,
    //         ]));

    //         $headers = array();
    //         $headers[] = 'Accept: */*';
    //         $headers[] = 'User-Agent: Thunder Client (https://www.thunderclient.com)';
    //         $headers[] = 'Content-Type: application/json';
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //         $result = curl_exec($ch);
    //         $err = curl_errno($ch);
    //         curl_close($ch);

    //         // echo "Result: " . $result . "\n"; // Print result
    //         // jika pengiriman gagal maka dianggap nomor tidak valid 

    //         $response = json_decode($result, true);
    //         if (isset($response['error']) && $response['error'] === 'Failed to send message') {
    //             // Log error atau tangani sesuai kebutuhan
    //             Log::error('cURL Error #: ' . $response['error']);
    //             DB::table('numbers')
    //                 ->where('id',  $item->id)
    //                 ->update(['status' => 'number not registered']);
    //             // jika repon dari api server "Message sent with session" maka nomro valid dan sudah terkirim
    //         } else if (isset($response['message']) && preg_match('/Message sent with session/', $response['message'])) {
    //             // Log atau tangani response sesuai kebutuhan
    //             Log::info('cURL Response: ' . $result);
    //             DB::table('numbers')
    //                 ->where('id',  $item->id)
    //                 ->update(['status' => 'terkirim']);
    //             // jika respon ga ada maka session habis dan menjadi status belum di kirim
    //         } else {
    //             // Log atau tangani response sesuai kebutuhan
    //             Log::info('cURL Response: ' . $result);
    //             DB::table('numbers')
    //                 ->where('id',  $item->id)
    //                 ->update(['status' => 'belum terkirim']);
    //         }
    //     }

    //     return response()->json($numbers);
    // }

    public function executeCurl()
    {
        $numbers = DB::table('numbers')
        ->join('messages', 'numbers.message_id', '=', 'messages.id')
        ->where('numbers.status', 'belum terkirim')
        ->select('numbers.*', 'messages.img', 'messages.message as message_text')
        ->get();

    foreach ($numbers as $item) {
        $ch = curl_init();

        // Bersihkan dan format pesan dari HTML ke plain text dengan format Markdown
        $formattedMessage = $item->message_text;

        // Ganti tag <p> dengan baris baru (\n)
        $message_with_newlines = str_replace(['<p>', '</p>'], ['', "\n"], $formattedMessage);

        // Hapus tag HTML lain (jika ada tag HTML selain <p>)
        $clean_message = strip_tags($message_with_newlines);

        curl_setopt($ch, CURLOPT_URL, 'http://147.139.201.32:60000/send-image');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'to' => $item->number,
            'text' => $clean_message,  // Kirim pesan yang sudah diformat
            'imageUrl' => $item->img,
        ]));

            $headers = array();
            $headers[] = 'Accept: */*';
            $headers[] = 'User-Agent: Thunder Client (https://www.thunderclient.com)';
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            $err = curl_errno($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            Log::info('Response for number ' . $item->number . ': ' . $result);

            // Cek error curl
            if ($err) {
                Log::error('cURL Error: ' . curl_error($ch));
                DB::table('numbers')
                    ->where('id', $item->id)
                    ->update(['status' => 'number not registered']);
                continue;  // Lanjut ke nomor berikutnya
            }

            // Cek status HTTP
            if ($httpcode !== 200) {
                Log::error('HTTP Error Code: ' . $httpcode);
                DB::table('numbers')
                    ->where('id', $item->id)
                    ->update(['status' => 'number not registered']);
                continue;
            }

            // Parse respons dari server
            $response = json_decode($result, true);

            // Jika ada error dalam respons dari server
            if (isset($response['error'])) {
                Log::error('Server Error: ' . $response['error']);
                DB::table('numbers')
                    ->where('id', $item->id)
                    ->update(['status' => 'number not registered']);
            } else if (isset($response['message']) && preg_match('/Message sent/', $response['message'])) {
                // Jika pesan berhasil dikirim
                DB::table('numbers')
                    ->where('id', $item->id)
                    ->update(['status' => 'terkirim']);
            } else {
                // Jika respons tidak dikenali
                Log::info('Unrecognized Response: ' . $result);
                DB::table('numbers')
                    ->where('id', $item->id)
                    ->update(['status' => 'belum terkirim']);
            }

            // Tambahkan jeda antar permintaan
            usleep(500000);  // 0.5 detik jeda antar pengiriman
        }

        return response()->json($numbers);
    }

}
