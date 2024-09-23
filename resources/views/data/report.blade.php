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
                                    <th>Tanggal</th>
                                    <th>Pesan Terkirim</th>
                                    <th>Pesan Di Terima</th>
                                    <th>Pesan Tidak Terkirim</th>
                                    <th>Pesan Sedang Di Proses</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#btnFilter').click(function() {
                var startDate = $('#startDate').val();
                var endDate = $('#endDate').val();

                if (startDate && endDate) {
                    $.ajax({
                        type: "GET",
                        url: "",
                        data: {
                            startDate: startDate,
                            endDate: endDate
                        },
                        success: function(data) {
                            $('#reportTableBody').empty();
                            if (data.length > 0) {
                                $.each(data, function(index, item) {
                                    $('#reportTableBody').append('<tr>' +
                                        '<td>' + item.date + '</td>' +
                                        '<td>' + item.sent + '</td>' +
                                        '<td>' + item.received + '</td>' +
                                        '<td>' + item.not_sent + '</td>' +
                                        '<td>' + item.processing + '</td>' +
                                        '</tr>');
                                });
                            } else {
                                $('#reportTableBody').append('<tr>' +
                                    '<td colspan="5" class="text-center">Tidak ada data yang ditemukan</td>' +
                                    '</tr>');
                            }
                        }
                    });
                } else {
                    alert('Tanggal mulai dan tanggal akhir harus diisi');
                }
            });

            // ketika halaman di load maka tampilkan semua data
            $.ajax({
                type: "GET",
                url: "",
                success: function(data) {
                    $('#reportTableBody').empty();
                    if (data.length > 0) {
                        $.each(data, function(index, item) {
                            $('#reportTableBody').append('<tr>' +
                                '<td>' + item.date + '</td>' +
                                '<td>' + item.sent + '</td>' +
                                '<td>' + item.received + '</td>' +
                                '<td>' + item.not_sent + '</td>' +
                                '<td>' + item.processing + '</td>' +
                                '</tr>');
                        });
                    } else {
                        $('#reportTableBody').append('<tr>' +
                            '<td colspan="5" class="text-center">Tidak ada data yang ditemukan</td>' +
                            '</tr>');
                    }
                }
            });
        });
    </script>
@endsection
