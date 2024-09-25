<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Message;
use App\Models\Number;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari request jika ada (filter dari frontend)
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Format tanggal untuk disesuaikan dengan format data di Firebase
        if ($startDate) {
            $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        }
        if ($endDate) {
            $endDate = date('Y-m-d H:i:s', strtotime($endDate));
        }

        // Buat HTTP Client untuk mengambil data dari Firebase
        $client = new Client();
        $response = $client->get('https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json');


        $messages = json_decode($response->getBody()->getContents(), true);

        // Filter pesan berdasarkan tanggal jika ada filter yang diberikan
        $filteredMessages = [];
        if ($startDate && $endDate) {
            foreach ($messages as $key => $message) {
                if ($message['create_at'] >= $startDate && $message['create_at'] <= $endDate) {
                    $filteredMessages[$key] = $message;
                }
            }
        } else {
            // Jika tidak ada filter, tampilkan semua pesan
            $filteredMessages = $messages;
        }

        // Hitung pesan terkirim berdasarkan tanggal
        $countSent = Number::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'terkirim')
            ->count();

        // Siapkan data untuk dikirim ke view
        $data = [
            'title' => 'Report',
            'number' => Number::all(),
            'message' => Message::all(),
            'filteredMessages' => $filteredMessages,
            'countSent' => $countSent,
            'countSend' => count($filteredMessages),
            'startDate' => $request->input('startDate'),  // Kirim ke view
            'endDate' => $request->input('endDate'),      // Kirim ke view
        ];

        return view('data.report', $data);
    }
}