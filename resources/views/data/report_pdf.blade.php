<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pesan</title>
    <style>
        /* Tambahkan gaya untuk PDF */
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Laporan Pesan</h1>
    <p>Total Tagihan: Rp {{ number_format($totalMessagesSent * 250, 0, ',', '.') }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pesan Terkirim</th>
                <th>Pesan Diterima</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $startDate }} - {{ $endDate }}</td>
                <td>{{ $totalMessagesSent }}</td>
                <td>{{ $totalMessagesReceived }}</td>
            </tr>
        </tbody>
    </table>
    <p style="text-align: right;">Pasuruan, {{ date('d F Y') }}</p>
    <br>
    <p>Kami,</p>
    <br>
    <p style="text-align: right;">Direktur WA BLAST</p>
</body>

</html>
