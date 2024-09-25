@extends('layouts.backend')

@section('content')
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Welcome Back Bruuhh! </h5>
                        <p class="mb-4">
                            Selamat datang kembali di website kami <span class="fw-bold">WA BLAST</span>. Kami juga
                            menyediakan beberapa fitur yang dapat anda gunakan, jika anda berminat silahkan hubungi
                            developer kami.
                        </p>
                        <a href="https://wa.me/6281554850403" class="btn btn-sm btn-outline-primary">Contact me</a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('template/templateAdmin/assets/img/illustrations/man-with-laptop-light.png') }}" height="170" alt="View Badge User" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
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

    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/diterima.png') }}" alt="Pesan Di Terima" class="rounded" />
                    </div>
                    <span class="fw-semibold">Pesan Di Terima</span>
                </div>
                <h3 class="card-title mb-2 text-center" id="totalMessages">0</h3>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/gagal.png') }}" alt="Pesan Tidak Terkirim" class="rounded" />
                    </div>
                    <span class="fw-semibold" style="margin-left: 20px">Pesan Tidak Terkirim</span>
                </div>
                <h3 class="card-title mb-2 text-center">{{ $jumlah_number_not_registered }}</h3>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <div class="avatar flex-shrink-0">
                        <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/reload.png') }}" alt="Pesan Sedang Di Proses" class="rounded" />
                    </div>
                    <span class="fw-semibold" style="margin-left: 20px">Pesan Sedang Di Proses</span>
                </div>
                <h3 class="card-title mb-2 text-center">{{ $pandding }}</h3>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    async function loadData() {
        const messagesApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/received.json';
        const countApiUrl = 'https://wa-server-19d35-default-rtdb.asia-southeast1.firebasedatabase.app/message_counts.json';

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

    // Call loadData when the page loads
    window.onload = loadData;
</script>