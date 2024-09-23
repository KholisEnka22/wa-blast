@extends('layouts.backend')

@section('content')
<div class="row">
    <!-- Pesan Terkirim -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/terkirim.png') }}" alt="Pesan Terkirim" class="rounded" />
                    </div>
                    <span class="fw-semibold" style="margin-left: 20px">Pesan Terkirim</span>
                </div>
                <h3 class="card-title mb-2 text-center" id="totalSentMessages">0</h3>
            </div>
        </div>
    </div>

    <!-- Pesan Diterima -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/diterima.png') }}" alt="Pesan Di Terima" class="rounded" />
                    </div>
                    <span class="fw-semibold">Pesan Diterima</span>
                </div>
                <h3 class="card-title mb-2 text-center" id="totalMessages">0</h3>
            </div>
        </div>
    </div>

    <!-- Pesan Tidak Terkirim -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/gagal.png') }}" alt="Pesan Tidak Terkirim" class="rounded" />
                    </div>
                    <span class="fw-semibold" style="margin-left: 20px">Pesan Tidak Terkirim</span>
                </div>
                <h3 class="card-title mb-2 text-center">10</h3>
            </div>
        </div>
    </div>

    <!-- Pesan Sedang Di Proses -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/reload.png') }}" alt="Pesan Sedang Di Proses" class="rounded" />
                    </div>
                    <span class="fw-semibold" style="margin-left: 20px">Pesan Sedang Di Proses</span>
                </div>
                <h3 class="card-title mb-2 text-center">30</h3>
            </div>
        </div>
    </div>
</div>
<div class="col-12 mb-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Pengumuman</h5>
            <p class="card-text">
                Fitur ini masih dalam tahap pengembangan, jadi masih banyak bug dan errornya.
                Mohon maaf atas ketidaknyamanannya.
            </p>
        </div>
    </div>
</div>

<script>
    async function loadData() {
        const messagesApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json';
        const countApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/message_counts.json';
        const deleteApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received/';

        try {
            const responseMessages = await fetch(messagesApiUrl);
            const messagesData = await responseMessages.json();

            const responseCount = await fetch(countApiUrl);
            const countData = await responseCount.json();

            // Step 1: Group messages by 'from' field and count total messages
            let totalMessages = 0;
            Object.entries(messagesData).forEach(([key, messageObj]) => {
                totalMessages += 1;
            });

            // Step 2: Calculate total sent messages
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

            // Step 3: Update the total messages and total sent messages display
            document.getElementById('totalMessages').innerText = totalMessages;
            document.getElementById('totalSentMessages').innerText = totalSentMessages;

        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    // Call loadData when the page loads and every 5 seconds
    window.onload = loadData;
    setInterval(loadData, 5000);
</script>
@endsection