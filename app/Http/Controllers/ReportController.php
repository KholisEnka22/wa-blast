<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Number;
use PDF;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari request (query string)
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Jika filter belum dilakukan, jangan tampilkan data
        if (!$startDate || !$endDate) {
            return view('data.report', [
                'title' => 'Report',
                'filteredMessages' => [], // Kosongkan data
                'countSent' => 0,          // Kosongkan hitungan
                'countSend' => 0,          // Kosongkan hitungan
                'startDate' => null,       // Tanggal tetap kosong
                'endDate' => null          // Tanggal tetap kosong
            ]);
        }

        // Format tanggal dari input untuk digunakan di filtering
        $startDateFormatted = date('Y-m-d H:i:s', strtotime($startDate));
        $endDateFormatted = date('Y-m-d H:i:s', strtotime($endDate));

        // Buat HTTP Client untuk mengambil data dari Firebase
        $client = new Client();
        $response = $client->get('https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json');
        $messages = json_decode($response->getBody()->getContents(), true);

        // Filter pesan berdasarkan tanggal
        $filteredMessages = $this->filterMessagesByDate($messages, $startDateFormatted, $endDateFormatted);

        // Hitung pesan terkirim berdasarkan tanggal
        $countSent = $this->countSentMessages($startDateFormatted, $endDateFormatted);

        // Jika permintaan AJAX, kirim JSON
        if ($request->ajax()) {
            return response()->json([
                'filteredMessages' => $filteredMessages,
                'countSent' => $countSent,
                'countSend' => count($filteredMessages),
            ]);
        }

        // Siapkan data untuk dikirim ke view
        return view('data.report', [
            'title' => 'Report',
            'filteredMessages' => $filteredMessages,
            'countSent' => $countSent,
            'countSend' => count($filteredMessages),
            'startDate' => $startDate, // Simpan tanggal asli untuk ditampilkan
            'endDate' => $endDate       // Simpan tanggal asli untuk ditampilkan
        ]);
    }

    /**
     * Filter messages by date range.
     */
    private function filterMessagesByDate(array $messages, $startDate, $endDate)
    {
        return array_filter($messages, function ($message) use ($startDate, $endDate) {
            if (!isset($message['create_at'])) {
                return false;
            }

            // Pengecekan tanggal dengan format yang sudah diterima dari Firebase
            return $message['create_at'] >= $startDate && $message['create_at'] <= $endDate;
        });
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

    public function printReport(Request $request)
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        // Format tanggal agar sesuai dengan format yang digunakan di Firebase dan filtering
        $startDateFormatted = \Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->toDateString();
        $endDateFormatted = \Carbon\Carbon::createFromFormat('Y-m-d', $endDate)->toDateString();

        // Ambil data laporan berdasarkan tanggal
        $data = $this->getAllMessages($startDateFormatted, $endDateFormatted);
        $totalMessagesSent = $this->countSentMessages($startDateFormatted, $endDateFormatted);

        // Pastikan data tidak kosong
        if (empty($data)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk dicetak.');
        }

        // Hitung jumlah pesan terkirim dan diterima
        $totalMessagesReceived = count(array_filter($data, function ($item) {
            return !empty($item['message']); // Hanya menghitung pesan yang memiliki konten
        }));

        // Buat PDF dari view dengan tambahan informasi jumlah pesan
        $pdf = PDF::loadView('data.report_pdf', [
            'content' => $data,
            'startDate' => $startDateFormatted,
            'endDate' => $endDateFormatted,
            'totalMessagesSent' => $totalMessagesSent,
            'totalMessagesReceived' => $totalMessagesReceived,
        ]);

        return $pdf->download('laporan_pesan.pdf');
    }

    private function getAllMessages($startDate, $endDate)
    {
        // Ambil data dari Firebase
        $client = new Client();
        $response = $client->get('https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json');
        $data = json_decode($response->getBody()->getContents(), true);

        // Ubah format tanggal menjadi objek Carbon untuk mempermudah perbandingan tanggal
        $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
        $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();

        // Memfilter data berdasarkan rentang tanggal
        $filteredData = array_filter($data, function ($message) use ($startDate, $endDate) {
            // Pastikan pesan memiliki tanggal 'create_at'
            if (!isset($message['create_at'])) {
                return false;
            }

            // Ubah 'create_at' dari pesan menjadi objek Carbon
            $messageDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $message['create_at']);

            // Cek apakah pesan berada dalam rentang tanggal yang difilter
            return $messageDate->between($startDate, $endDate);
        });

        return $filteredData; // Mengembalikan pesan yang difilter
    }
}
