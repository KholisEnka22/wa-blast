@extends('layouts.backend')

@section('content')
<div class="card mb-4">
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
        <h5 class="card-header">Daftar Server</h5>
        <div class="d-flex justify-content-end mb-3">
            <form class="d-flex">
                <input type="text" id="search" class="form-control" placeholder="Search..." autocomplete="off">
            </form>
            <button id="myButton" class="btn btn-icon btn-primary ms-2" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right" data-bs-html="true" title="<span>Tambahkan Server</span>">
                <span class="tf-icons bx bx-plus-medical"></span>
            </button>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover " id="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Server</th>
                        <th>Pesan Terkirim</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if (count($server) > 0)
                    @foreach ($server as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>Server {{ $loop->iteration }}</td>
                        <td>{{ $item->count }}</td>
                        <td>
                            <form action="{{ route('setting.toggle-server', ['id' => $item->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn badge {{ $item->status === 'Active' ? 'bg-label-success' : 'bg-label-danger' }} me-1" onclick="return confirm('Apakah Anda yakin ingin mengubah status server?')">
                                    {{ $item->status === 'Active' ? 'Active' : 'Non Active' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('server.destroy', ['id' => $item->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-rounded btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus server?')">
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

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="basicModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Configuration server</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('server.store') }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="add">Private Key</label>
                            <input type="text" name="server" class="form-control" placeholder="Private Key">
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="add">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password">
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
    document.getElementById('myButton').addEventListener('click', function() {
        var tooltip = new bootstrap.Tooltip(this); // Aktifkan tooltip manual
        var modal = new bootstrap.Modal(document.getElementById('basicModal')); // Aktifkan modal manual
        modal.show();
    });
</script>
@endsection