@extends('layouts.backend')

@section('content')
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
        <h5 class="card-header">Daftar Inbox</h5>
        <div class="d-flex justify-content-between mb-3" style="padding: 10px">
            <div class="" id="totalMessages"></div>
            <form class="d-flex">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari pesan..."
                    style="max-width: 300px;">
            </form>
        </div>


        <div class="table-responsive text-nowrap">
            <table class="table table-hover" id="messagesTable">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be inserted here -->
                </tbody>
            </table>
        </div>
    </div>

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
@endsection

@section('footer')
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        const messagesApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json';
        const countApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/message_counts.json';
        const deleteApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received/';

        function showNotification(message, type) {
            const notificationDiv = document.getElementById('notification');
            notificationDiv.textContent = message;
            notificationDiv.className = `alert alert-${type}`;
            notificationDiv.style.display = 'block';
        }

        async function deleteMessage(messageId) {
            try {
                const response = await fetch(`${deleteApiUrl}${messageId}.json`, {
                    method: 'DELETE'
                });
                if (!response.ok) {
                    throw new Error('Failed to delete message');
                }
                loadData();
                showNotification('Message deleted successfully!', 'success');
            } catch (error) {
                showNotification('Error deleting message: ' + error.message, 'danger');
            }
        }

        function filterMessages() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const tableBody = document.querySelector('#messagesTable tbody');
            const rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const fromColumn = rows[i].getElementsByTagName('td')[0];
                const messageColumn = rows[i].getElementsByTagName('td')[1];
                const fromText = fromColumn.textContent.toLowerCase();
                const messageText = messageColumn.textContent.toLowerCase();

                if (fromText.includes(searchInput) || messageText.includes(searchInput)) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        function limitText(text, limit) {
            return text.length > limit ? text.substring(0, limit) + '...' : text;
        }

        async function loadData() {
            try {
                const responseMessages = await fetch(messagesApiUrl);
                const messagesData = await responseMessages.json();

                const responseCount = await fetch(countApiUrl);
                const countData = await responseCount.json();

                const tableBody = document.querySelector('#messagesTable tbody');
                const totalMessagesDiv = document.getElementById('totalMessages');
                tableBody.innerHTML = '';

                const messagesArray = Object.entries(messagesData).reverse();
                let totalMessages = 0;

                for (const [key, messageObj] of messagesArray) {
                    const {
                        from,
                        message
                    } = messageObj;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                    <td>${from}</td>
                    <td>${limitText(message, 30)}</td>
                    <td>
                        <a href="#" class="text-warning ms-3" onclick="replyMessage('${key}', '${from}')" title="Balas Pesan">
                            <i class="bx bx-message-rounded-edit"></i>
                        </a>
                        <a href="#" class="text-danger" onclick="deleteMessage('${key}')" title="Hapus Pesan">
                            <i class="bx bx-trash"></i>
                        </a>
                    </td>
                `;
                    tableBody.appendChild(row);
                    totalMessages++;
                }

                let totalCount = 0;
                for (const key in countData) {
                    if (countData.hasOwnProperty(key)) {
                        totalCount += countData[key].count;
                    }
                }

                totalMessagesDiv.innerHTML = `
                <div class="message-summary">
                    
                    <button class="btn btn-primary">
                              Total Inbox
                              <span class="badge bg-white text-primary rounded-pill">${totalMessages}</span>
                            </button>
                    <button class="btn btn-primary">
                              Total Pesan Terkirim
                              <span class="badge bg-white text-primary rounded-pill">${totalCount}</span>
                            </button>
                </div>`;

            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        function handleStatus() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'sukses') {
                showNotification('Operasi berhasil! Pesan terkirim dan dihitung.', 'success');
            } else if (status === 'gagal') {
                showNotification('Operasi gagal. Mohon coba lagi.', 'danger');
            } else if (status === 'error') {
                showNotification('Parameter tidak lengkap. Mohon coba lagi.', 'warning');
            }
        }

        window.onload = function() {
            loadData();
            handleStatus();
            document.getElementById('searchInput').addEventListener('keyup', filterMessages);
        };
    </script>
@endsection
