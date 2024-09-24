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
                            <input type="text" class="form-control datepicker-here" id="startDate" name="startDate" placeholder="Tanggal Mulai" data-language='id' data-multiple-dates-separator=", " data-date-format="dd MM yyyy" autocomplete="off">
                            <label for="startDate">Tanggal Mulai</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control datepicker-here" id="endDate" name="endDate" placeholder="Tanggal Akhir" data-language='id' data-multiple-dates-separator=", " data-date-format="dd MM yyyy" autocomplete="off">
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
                                <th>Total Pesan DIterima</th>
                                <th>Cetak</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            @if ($countSent > 0)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</td>
                                <td>{{ $countSent }}</td>
                                <td>{{ $countSend }}</td>
                                <td>
                                    <button type="button" class="btn btn-primary" onclick="window.open(`{{ url('report/pdf', [$startDate, $endDate]) }}`, '_blank')">
                                        <i class="bx bx-printer"></i> Cetak
                                    </button>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="3" class="text-center">Tidak ada data yang ditemukan</td>
                            </tr>
                            @endif
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

            // Validasi jika tanggal mulai lebih besar dari tanggal akhir
            if (new Date(startDate) > new Date(endDate)) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return;
            }

            if (startDate && endDate) {
                window.location.href = "{{ url('/report') }}" + "?startDate=" + startDate + "&endDate=" + endDate;
            } else {
                alert('Tanggal mulai dan tanggal akhir harus diisi');
            }
        });
    });
</script>
@endsection