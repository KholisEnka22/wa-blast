@extends('layouts.backend')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/terkirim.png') }}"
                                alt="Pesan Terkirim" class="rounded" />
                        </div>
                        <span class="fw-semibold" style="margin-left: 20px">Pesan Terkirim</span>
                    </div>
                    <h3 class="card-title mb-2 text-center">100</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/diterima.png') }}"
                                alt="Pesan Di Terima" class="rounded" />
                        </div>
                        <span class="fw-semibold">Pesan Di Terima</span>
                    </div>
                    <h3 class="card-title mb-2 text-center">50</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/gagal.png') }}"
                                alt="Pesan Tidak Terkirim" class="rounded" />
                        </div>
                        <span class="fw-semibold" style="margin-left: 20px">Pesan Tidak Terkirim</span>
                    </div>
                    <h3 class="card-title mb-2 text-center">10</h3>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <div class="avatar flex-shrink-0">
                            <img src="{{ asset('template/templateAdmin/assets/img/icons/unicons/reload.png') }}"
                                alt="Pesan Sedang Di Proses" class="rounded" />
                        </div>
                        <span class="fw-semibold" style="margin-left: 20px">Pesan Sedang Di Proses</span>
                    </div>
                    <h3 class="card-title mb-2 text-center">30</h3>
                </div>
            </div>
        </div>
    </div>
@endsection
