@extends('layouts.backend')

@section('top')
<style>
    .chat-container {
        max-height: 70vh;
    }

    .chat-message {
        display: flex;
        flex-direction: column;
    }

    .chat-bubble {
        background-color: #e1ffc7;
        border-radius: 15px;
        padding: 10px;
        max-width: 80%;
    }

    .received-message {
        background-color: #f1f1f1;
    }

    .sent-message {
        background-color: #daf8cb;
        align-self: flex-end;
        /* Align pesan yang dikirim ke kanan */
    }

    .message-input {
        padding: 10px;
        border-top: 1px solid #ddd;
        background-color: #f9f9f9;
    }

    .avatar-initial {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-container::-webkit-scrollbar {
        width: 5px;
    }

    .chat-container::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }

    .btn-primary i {
        margin: 0;
        font-size: 20px;
    }

    /* Tambahkan margin kiri antara waktu dan nomor */
    .chat-bubble .text-muted {
        margin-left: auto;
    }
</style>
@endsection

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
    <div class="d-flex justify-content-between mb-3" style="padding: 10px">
        <div class="" id="totalMessages"></div>
        <form class="d-flex">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari pesan..." style="max-width: 300px;">
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


<!-- offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasEndLabel" class="offcanvas-title">Balas Pesan</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between">
        <!-- Chat List -->
        <div class="chat-container flex-grow-1 mb-3">
            <!-- Tempat untuk menampilkan balasan -->
            <div id="replyMessages"></div> <!-- Balasan pesan ditampilkan di sini -->
        </div>
    </div>

    <!-- Message Input -->
    <div class="message-input d-flex align-items-center">
        <input type="hidden" id="sessionInput" value="session_id_value"> <!-- Set session ID value dynamically -->
        <input type="hidden" id="fromInput" value="from_value"> <!-- Set from value dynamically -->
        <input id="replyInput" type="text" class="form-control me-2" placeholder="Tulis pesan di sini" onkeydown="if (event.keyCode == 13) document.getElementById('sendReply').click()">
        <button id="sendReply" type="button" class="btn btn-primary">
            <i class="bx bx-send"></i>
        </button>
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

            // Step 1: Group messages by 'from' field
            const groupedMessages = {};
            Object.entries(messagesData).forEach(([key, messageObj]) => {
                const {
                    from
                } = messageObj;

                // Debug: Log to check if the 'from' field is correct
                console.log(`Processing message from: ${from}`, messageObj);

                if (!groupedMessages[from]) {
                    groupedMessages[from] = {
                        count: 0,
                        keys: []
                    };
                }
                groupedMessages[from].count += 1;
                groupedMessages[from].keys.push(key); // Store message keys for future actions (reply/delete)
            });

            // Step 2: Display grouped data in the table
            let totalMessages = 0;
            for (const [from, group] of Object.entries(groupedMessages)) {
                const row = document.createElement('tr');
                row.innerHTML = `
                <td>${from}</td>
                <td>${group.count}</td> <!-- Display total count of messages -->
                <td>
                    <a href="#" class="text-danger" onclick="deleteMessage('${group.keys[0]}')" title="Hapus Pesan">
                        <i class="bx bx-trash"></i>
                    </a>
                    <a href="#" class="text-primary ms-3" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEnd" aria-controls="offcanvasEnd" onclick="replyMessage('${from}')" title="Balas Pesan">
                        <i class="bx bx-message-rounded-edit"></i>
                    </a>
                </td>
            `;
                tableBody.appendChild(row);
                totalMessages += group.count; // Add the count of messages from this group
            }

            // Step 3: Calculate total sent messages
            let totalSentMessages = 0;
            if (Array.isArray(countData)) {
                totalSentMessages = countData.reduce((sum, item) => sum + (item.count || 0), 0);
            } else if (typeof countData === 'object') {
                for (const key in countData) {
                    if (countData.hasOwnProperty(key)) {
                        totalSentMessages += countData[key].count || 0;
                    }
                }
            }

            // Step 4: Update the total messages display
            totalMessagesDiv.innerHTML = `
            <div class="message-summary">
                <button class="btn btn-primary">
                    Total Inbox
                    <span class="badge bg-white text-primary rounded-pill">${totalMessages}</span>
                </button>
                <button class="btn btn-primary">
                    Total Pesan Terkirim
                    <span class="badge bg-white text-primary rounded-pill">${totalSentMessages}</span>
                </button>
            </div>
        `;

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

<script>
    document.getElementById('sendReply').addEventListener('click', function() {
        // Ambil data dari input
        const sessionId = document.getElementById('sessionInput').value;
        const from = document.getElementById('fromInput').value;
        const messageText = document.getElementById('replyInput').value;

        console.info('Sending reply:', {
            sessionId,
            from,
            messageText
        });

        if (messageText.trim() === '') {
            showNotification('Please enter a message before sending.', 'warning');
            return;
        }

        // Contoh URL untuk mengirim data, sesuaikan dengan API Anda
        // iki rencana e gawe api curl biar bisa input ke database juga

        const replyApiUrl = 'https://example.com/api/send-reply';

        // Kirim data ke server
        fetch(replyApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    session: sessionId,
                    from: from,
                    text: messageText
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Tindakan setelah balasan berhasil dikirim
                showNotification('Reply sent successfully!', 'success');
                document.getElementById('replyInput').value = ''; // Kosongkan input
                // Tambahkan logika lain jika diperlukan
            })
            .catch(error => {
                showNotification('Error sending reply: ' + error.message, 'danger');
            });
    });

    // Fungsi untuk menampilkan notifikasi
    function showNotification(message, type) {
        const notificationDiv = document.getElementById('notification');
        notificationDiv.textContent = message;
        notificationDiv.className = `alert alert-${type}`;
        notificationDiv.style.display = 'block';
    }

    function replyMessage(from) {
        const replyMessages = document.getElementById('replyMessages');
        const replyInput = document.getElementById('replyInput');

        // Mengosongkan balasan sebelumnya
        replyMessages.innerHTML = '';

        // Ambil semua pesan dari pengirim
        fetch(`${messagesApiUrl}`)
            .then(response => response.json())
            .then(data => {
                // Filter pesan berdasarkan 'from'

                const filteredMessages = Object.entries(data).filter(([key, messageObj]) => messageObj.from === from);

                if (filteredMessages.length === 0) {
                    showNotification('No messages found from this sender.', 'warning');
                }

                // Tampilkan semua pesan yang diterima
                filteredMessages.forEach(([key, messageObj], index, array) => {
                    const message = messageObj.message;
                    if (index === array.length - 1) {
                        const sessionKey = messageObj.session;
                        console.info(sessionKey);
                        document.getElementById('sessionInput').value = sessionKey;
                    }
                    document.getElementById('fromInput').value = from;
                    const receivedMessage = document.createElement('div');
                    receivedMessage.classList.add('chat-message', 'mb-2');
                    receivedMessage.innerHTML = `
                <div class="d-flex">
                    <div class="avatar flex-shrink-0 me-2">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                    <div class="chat-bubble received-message">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">${from}</h6>
                            <small class="text-muted">Just now</small>
                        </div>
                        <p class="mb-0">${message}</p>
                    </div>
                </div>
`;

                    replyMessages.appendChild(receivedMessage);
                });

                // Set focus ke input balasan
                replyInput.focus();
            })
            .catch(error => {
                showNotification('Error fetching messages from sender: ' + error.message, 'danger');
            });
    }
</script>


@endsection