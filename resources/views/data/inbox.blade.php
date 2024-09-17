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
    <h5 class="card-header">Daftar Inbox</h5>
    <div class="d-flex justify-content-end mb-3 mr-10">
        <form>
            <input type="text" id="search" class="form-control" placeholder="Search..." autocomplete="off">
        </form>
        <button id="myButton" class="btn btn-icon btn-primary float-end ms-2 me-2" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right" data-bs-html="true" title="<span>Tambah Template Pesan</span>">
            <span class="tf-icons bx bx-plus-medical"></span>
        </button>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-hover" id="data-table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>No.Wa</th>
                    <th>Inbox</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                {{-- @foreach ($murid as $m) --}}
                <tr>
                    <td>1</td>
                    <td>085731028605</td>
                    <td>Halo</td>
                    <td>12-02-2025</td>
                    <td>Balas</td>
                </tr>
                {{-- @endforeach --}}
            </tbody>
        </table>
    </div>
</div>
<!--/ Hoverable Table rows -->

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="basicModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Tambah Tempate Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('message.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="add">Url Gambar</label>
                            <input type="text" name="img_url" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="add">Pesan</label>
                            <textarea name="message" id="summernote">Tulis pesan disini</textarea>
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
{{-- End Modal --}}
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
    document.getElementById('myButton').addEventListener('click', function() {
        var tooltip = new bootstrap.Tooltip(this); // Aktifkan tooltip manual
        var modal = new bootstrap.Modal(document.getElementById('basicModal')); // Aktifkan modal manual
        modal.show();
    });
</script>
@endsection