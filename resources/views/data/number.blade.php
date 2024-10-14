@extends('layouts.backend')

@section('content')
<!-- Elemen untuk menampilkan pesan toast, awalnya tersembunyi -->

@if (session('message'))
<div class="bs-toast toast toast-placement-ex m-2 {{ session('message.type') === 'success' ? 'bg-success' : 'bg-danger' }}" id="modalMessageToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
    <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <div class="me-auto fw-semibold">
            {{ session('message.type') === 'success' ? 'Sukses' : 'Error' }}
        </div>
        <small>just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        {{ session('message.content') }}
    </div>
</div>
@endif

<div class="card" style="padding: 10px">
    <h5 class="card-header">Daftar No Whatsapp</h5>
    <div class="d-flex justify-content-end mb-3">
        <form class="d-flex">
            <input type="text" id="search" class="form-control" placeholder="Search..." autocomplete="off">
        </form>
        <button id="myButton" class="btn btn-icon btn-primary ms-2" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right" data-bs-html="true" title="<span>Kirim Pesan Massal</span>">
            <span class="tf-icons bx bxs-paper-plane"></span>
        </button>
    </div>
    @if ($pandding === 0)
    <span style="margin-top: 20px; font-size: 1.2rem" class="badge rounded-pill bg-label-success me-2">Tidak ada yang di proses</span>
    @else
    <span style="margin-top: 20px; font-size: 1.2rem" class="badge rounded-pill bg-label-danger me-2">{{ $pandding }} sedang diproses</span>
    @endif

    <div class="table-responsive text-nowrap">
        <table class="table table-hover " id="data-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>No.Wa</th>
                    <th>Gambar</th>
                    <th>Pesan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @foreach ($number as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->number }}</td>
                    <td>
                        @if ($item->message)
                        <img src="{{ $item->message->img }}" alt="Gambar Pesan" style="max-width: 90px; height: 90px;">
                        @else
                        <span>Messsage sudah dihapus</span>
                        @endif
                    </td>
                    <td>
                        @if ($item->message)
                        {{ \Illuminate\Support\Str::limit(strip_tags($item->message->message), 25, '...') }}
                        @else
                        <span>Messsage sudah dihapus</span>
                        @endif
                    </td>

                    <td>{{ $item->created_at }}</td>
                    <td>
                        <span class="badge {{ $item->status == 'terkirim' ? 'bg-label-success' : 'bg-label-danger' }}">
                            @if ($item->status == 'terkirim')
                            Terkirim
                            @elseif ($item->status == 'belum terkirim')
                            Belum Terkirim
                            @else
                            Number not registered
                            @endif
                        </span>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        @if ($number->hasPages())
        <nav aria-label="Page navigation" style="margin-top: 10px">
            <ul class="pagination justify-content-center">
                <!-- Tombol Previous -->
                @if ($number->onFirstPage())
                <li class="page-item prev disabled">
                    <a class="page-link" href="javascript:void(0);"><i class="tf-icon bx bx-chevrons-left"></i></a>
                </li>
                @else
                <li class="page-item prev">
                    <a class="page-link" href="{{ $number->previousPageUrl() }}"><i class="tf-icon bx bx-chevrons-left"></i></a>
                </li>
                @endif

                <!-- Tombol Angka Halaman -->
                @foreach ($number->getUrlRange(1, $number->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $number->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                <!-- Tombol Next -->
                @if ($number->hasMorePages())
                <li class="page-item next">
                    <a class="page-link" href="{{ $number->nextPageUrl() }}"><i class="tf-icon bx bx-chevrons-right"></i></a>
                </li>
                @else
                <li class="page-item next disabled">
                    <a class="page-link" href="javascript:void(0);"><i class="tf-icon bx bx-chevrons-right"></i></a>
                </li>
                @endif
            </ul>
        </nav>
        @endif

    </div>
</div>
</div>
<!--/ Hoverable Table rows -->

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="basicModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Kirim Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('number.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="add">Tambah Nomor</label>
                            <textarea name="numbers" class="form-control" placeholder="628....&#10;628....&#10;..."></textarea>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="add">Pesan</label>
                            <select name="message_id" id="message_id" class="form-select">
                                <option selected disabled>Pilih Pesan</option>
                                @foreach ($message as $item)
                                <option value="{{ $item->id }}">{{ $item->short_message }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('footer')
<script>
    $(document).ready(function() {
        var message = @json(session('message'));

        if (typeof message === 'object' && message !== null) {
            var messageType = message.type;
            var messageContent = message.content;

            if (messageType === 'success' || messageType === 'danger') {
                $('#modalMessageToast .fw-semibold').text(messageType.charAt(0).toUpperCase() + messageType
                    .slice(1));
                $('#modalMessageToast .toast-body').text(messageContent);

                $('#modalMessageToast').toast('show');
            }
        }
    });
</script>
<script>
    function updatePandding() {
        $.ajax({
            url: "{{ route('number.index') }}",
            type: 'GET',
            success: function(data) {
                console.log(data); // Tambahkan ini untuk melihat data yang diterima
                if (data.pandding !== undefined) {
                    $('#pandding').html(data.pandding + ' sedang diproses');
                } else {
                    console.error("Property 'pandding' tidak ditemukan di response.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
            }
        });
    }
    updatePandding();
    setInterval(updatePandding, 2000);
</script>
<script>
    document.getElementById('myButton').addEventListener('click', function() {
        var tooltip = new bootstrap.Tooltip(this); // Aktifkan tooltip manual
        var modal = new bootstrap.Modal(document.getElementById('basicModal')); // Aktifkan modal manual
        modal.show();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/execute-curl')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('cURL executed successfully');
                } else {
                    console.error('cURL execution failed');
                }
            })
            .catch(error => console.error('Error:', error));
    });
</script>
@endsection