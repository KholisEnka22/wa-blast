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
    <p>Rentang Tanggal: {{ $startDate }} sampai {{ $endDate }}</p>
    <p>Jumlah Pesan Terkirim: {{ $totalMessagesSent }}</p>
    <p>Jumlah Pesan Diterima: {{ $totalMessagesReceived }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pengirim</th>
                <th>Pesan</th>
                <th>Sesi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($content as $message)
                <tr>
                    <td>{{ $message['create_at'] }}</td>
                    <td>{{ $message['from'] }}</td>
                    <td>{{ $message['message'] }}</td>
                    <td>{{ $message['session'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
