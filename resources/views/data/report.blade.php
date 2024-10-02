@extends('layouts.backend')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Laporan Pesan</h5>

                    <!-- Filter -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control datepicker-here" id="startDate" name="startDate"
                                    placeholder="Tanggal Mulai" data-language='id' data-multiple-dates-separator=", "
                                    data-date-format="dd MM yyyy" autocomplete="off">
                                <label for="startDate">Tanggal Mulai</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control datepicker-here" id="endDate" name="endDate"
                                    placeholder="Tanggal Akhir" data-language='id' data-multiple-dates-separator=", "
                                    data-date-format="dd MM yyyy" autocomplete="off">
                                <label for="endDate">Tanggal Akhir</label>
                            </div>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="btnFilter" class="btn btn-primary">
                                <i class="bx bx-filter-alt"></i> Filter
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive text-nowrap">
                        <table id="reportTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal Start</th>
                                    <th>Tanggal End</th>
                                    <th>Total Pesan Terkirim</th>
                                    <th>Total Pesan Diterima</th>
                                    <th>Cetak</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Data akan ditambahkan melalui AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(document).ready(function() {
            $('#btnFilter').click(function() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();

                // Validasi jika tanggal mulai lebih besar dari tanggal akhir
                if (new Date(startDate) > new Date(endDate)) {
                    alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                    return;
                }

                if (startDate && endDate) {
                    // Konversi format tanggal dari 'dd MMMM yyyy' ke 'yyyy-mm-dd'
                    var formattedStartDate = formatDate(startDate);
                    var formattedEndDate = formatDate(endDate);

                    // Update URL tanpa memuat ulang halaman menggunakan tanggal yang diformat
                    const newUrl =
                        `{{ url('/report') }}?startDate=${encodeURIComponent(formattedStartDate)}&endDate=${encodeURIComponent(formattedEndDate)}`;
                    window.history.pushState({
                        path: newUrl
                    }, '', newUrl); // Memperbarui URL di address bar

                    // Menampilkan spinner loading
                    $('#reportTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);

                    $.ajax({
                        url: newUrl,
                        type: "GET",
                        success: function(response) {
                            $('#reportTableBody').empty(); // Kosongkan tabel sebelumnya

                            // Tambahkan baris baru ke tabel
                            if (response.countSent > 0) {
                                $('#reportTableBody').append(`
                            <tr>
                                <td>${formattedStartDate}</td>
                                <td>${formattedEndDate}</td>
                                <td>${response.countSent}</td>
                                <td>${response.countSend}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" id="btnPrint">
                                        <i class="bx bx-printer"></i> Cetak
                                    </button>
                                </td>
                            </tr>
                        `);
                            } else {
                                $('#reportTableBody').append(`
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data yang ditemukan</td>
                            </tr>
                        `);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error fetching data:', xhr);
                        }
                    });
                } else {
                    alert('Tanggal mulai dan tanggal akhir harus diisi');
                }
            });

            function formatDate(dateStr) {
                const months = {
                    'Januari': '01',
                    'Februari': '02',
                    'Maret': '03',
                    'April': '04',
                    'Mei': '05',
                    'Juni': '06',
                    'Juli': '07',
                    'Agustus': '08',
                    'September': '09',
                    'Oktober': '10',
                    'November': '11',
                    'Desember': '12'
                };

                const parts = dateStr.split(' ');
                const day = parts[0].padStart(2, '0');
                const month = months[parts[1]];
                const year = parts[2];

                return `${year}-${month}-${parseInt(day, 10)}`; // Menghasilkan format yyyy-mm-dd
            }

            // Event listener untuk tombol cetak
            $(document).on('click', '#btnPrint', function() {
                // Ambil nilai dari input tanggal
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();

                // Konversi format tanggal dari 'dd MMMM yyyy' ke 'yyyy-mm-dd'
                var formattedStartDate = formatDate(startDate);
                var formattedEndDate = formatDate(endDate);

                if (formattedStartDate && formattedEndDate) {
                    // Membuat URL cetak
                    var printUrl =
                        `{{ url('/report/print') }}?startDate=${encodeURIComponent(formattedStartDate)}&endDate=${encodeURIComponent(formattedEndDate)}`;

                    // Buka PDF di jendela baru
                    window.open(printUrl, '_blank'); // Membuka PDF di tab baru
                } else {
                    alert('Tanggal mulai dan tanggal akhir harus diisi');
                }
            });

            // Fungsi untuk mengonversi format tanggal
            function formatDate(dateStr) {
                const months = {
                    'Januari': '01',
                    'Februari': '02',
                    'Maret': '03',
                    'April': '04',
                    'Mei': '05',
                    'Juni': '06',
                    'Juli': '07',
                    'Agustus': '08',
                    'September': '09',
                    'Oktober': '10',
                    'November': '11',
                    'Desember': '12'
                };

                const parts = dateStr.split(' ');
                const day = parts[0].padStart(2, '0');
                const month = months[parts[1]];
                const year = parts[2];

                return `${year}-${month}-${parseInt(day, 10)}`; // Menghasilkan format yyyy-mm-dd
            }
        });
    </script>
@endsection
