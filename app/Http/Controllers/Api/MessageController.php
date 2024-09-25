<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    // Fungsi untuk menampilkan data dengan status 'belum terkirim'
    public function getPendingNumber()
    {
        $number = DB::table('numbers')
        ->join('messages', 'numbers.message_id', '=', 'messages.id')  // Join ke tabel messages
        ->where('numbers.status', 'belum terkirim')  // Kondisi 'belum terkirim'
        ->select('numbers.*', 'messages.message as message_text','messages.img')  // Pilih semua kolom dari numbers dan pesan dari messages
        ->first();

        // Cek apakah ada data yang ditemukan
        if ($number) {
        // Return data yang ditemukan
        return response()->json([
            'message' => 'Data ditemukan.',
            'data' => $number
        ], 200);
        }

        // Jika tidak ada data ditemukan
        return response()->json([
        'message' => 'Tidak ada data dengan status "belum terkirim".'
        ], 404);
    }

    // Fungsi untuk mengubah status menjadi 'terkirim'
    public function updateNumberStatus($id)
    {
        // Cek apakah ada data dengan id tersebut
        $number = DB::table('numbers')->where('id', $id)->first();

        // Jika data ditemukan
        if ($number && $number->status === 'belum terkirim') {
            // Ubah status menjadi 'terkirim'
            DB::table('numbers')
                ->where('id', $id)
                ->update(['status' => 'terkirim']);

            // Return response sukses
            return response()->json([
                'message' => 'Status berhasil diubah menjadi terkirim.',
                'data' => $number
            ], 200);
        }

        // Jika data tidak ditemukan atau status sudah 'terkirim'
        return response()->json([
            'message' => 'Data tidak ditemukan atau status sudah "terkirim".'
        ], 404);
    }
}
