@extends('layouts.backend')

@section('content')
    <div class="card mb-4">
        <!-- Elemen untuk menampilkan pesan toast, awalnya tersembunyi -->

        @if (session('message'))
            <div class="bs-toast toast toast-placement-ex m-2 {{ session('message.type') === 'success' ? 'bg-success' : 'bg-danger' }}"
                id="modalMessageToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
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
            <h5 class="card-header">Daftar Pesan</h5>
            <div class="d-flex justify-content-end mb-3">
                <form class="d-flex">
                    <input type="text" id="search" class="form-control" placeholder="Search..." autocomplete="off">
                </form>
                <button id="pesan" class="btn btn-icon btn-primary ms-2" data-bs-toggle="tooltip" data-bs-offset="0,4"
                    data-bs-placement="right" data-bs-html="true" title="<span>Tambahkan Pesan</span>">
                    <span class="tf-icons bx bx-plus-medical"></span>
                </button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover " id="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Image</th>
                            <th>Pesan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($pesan) > 0)
                            @foreach ($pesan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ $item->img }}" alt="Gambar Pesan"
                                            style="max-width: 90px; height: 90px;">
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit(strip_tags($item->message), 25, '...') }}</td>
                                    <td>
                                        <form action="{{ route('message.destroy', ['id' => $item->id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-icon btn-rounded btn-outline-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus pesan?')">
                                                <i class="tf-icons bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- modal tambah pesan -->
    <div class="modal fade" id="pesanModal" tabindex="-1" aria-labelledby="pesanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Isi Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('message.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea class="form-control" name="message" placeholder="Tulis pesan disini" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="img_url">Image URL:</label>
                            <input type="text" class="form-control" id="img_url" name="img_url"
                                placeholder="https://example.com/image.jpg" required oninput="loadImage(this.value)">
                        </div>

                        <div class="form-group">
                            <img src="" id="imgPreview" style="max-width: 200px; height: auto;" />
                        </div>

                        <script>
                            function loadImage(url) {
                                var img = document.getElementById('imgPreview');
                                img.src = url;
                            }
                        </script>
                        <br>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
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
        document.getElementById('pesan').addEventListener('click', function() {
            var tooltip = new bootstrap.Tooltip(this); // Aktifkan tooltip manual
            var modal = new bootstrap.Modal(document.getElementById('pesanModal')); // Aktifkan modal manual
            modal.show();
        });
    </script>
@endsection
