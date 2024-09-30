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
        $startDate = $startDate ? date('Y-m-d H:i:s', strtotime($startDate)) : null;
        $endDate = $endDate ? date('Y-m-d H:i:s', strtotime($endDate)) : null;

        // Buat HTTP Client untuk mengambil data dari Firebase
        $client = new Client();
        $response = $client->get('https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json');
        $messages = json_decode($response->getBody()->getContents(), true);

        // Filter pesan berdasarkan tanggal jika ada filter yang diberikan
        $filteredMessages = $this->filterMessagesByDate($messages, $startDate, $endDate);

        // Hitung pesan terkirim berdasarkan tanggal
        $countSent = $this->countSentMessages($startDate, $endDate);

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

    /**
     * Filter messages by date range.
     */
    private function filterMessagesByDate(array $messages, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return array_filter($messages, function ($message) use ($startDate, $endDate) {
                return $message['create_at'] >= $startDate && $message['create_at'] <= $endDate;
            });
        }

        // Jika tidak ada filter, kembalikan semua pesan
        return $messages;
    }

    /**
     * Count sent messages based on date range.
     */
    private function countSentMessages($startDate, $endDate)
    {
        return Number::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'terkirim')
            ->count();
    }

}