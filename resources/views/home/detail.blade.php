<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $branchStore->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $branchStore->logo) }}">    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts (Montserrat untuk kesan sporty & modern) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            /* Mengambil warna dari Logo LEVELFIT */
            --primary-lime: #D6E927; /* Warna LIME pada teks LEVEL */
            --primary-red: #E62E2D;  /* Warna MERAH pada teks FIT/Tagline */
            --dark-grey: #333333;    /* Warna elemen bar di tengah */
            --bg-dark: #1a1a1a;
            --text-light: #f8f9fa;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            color: #333;
            overflow-x: hidden;
            /* Tambahkan padding-top pada body agar konten tidak tertutup navbar fixed */
            padding-top: 80px; 
        }

        .container {
            width: calc(100% - 32px);
            max-width: 1680px;
        }

        @media (max-width: 576px) {
            .container {
                width: calc(100% - 20px);
            }
        }

        /* --- Navbar Custom --- */
        .navbar {
            background-color: rgba(255, 255, 255, 0.98); /* Lebih solid */
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 10px 0; /* Sedikit lebih compact */
            transition: all 0.3s;
            height: 80px; /* Tinggi pasti untuk kalkulasi layout */
        }

        .navbar-brand img {
            max-height: 50px; /* Sesuaikan ukuran logo */
        }

        .nav-link {
            font-weight: 600;
            color: var(--dark-grey) !important;
            margin-right: 15px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .nav-link:hover {
            color: var(--primary-red) !important;
        }

        .btn-login {
            border: 2px solid var(--primary-red);
            color: var(--primary-red);
            font-weight: 700;
            border-radius: 30px;
            padding: 8px 25px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: var(--primary-red);
            color: white;
        }

        /* --- Hero Section (DIPERBAIKI) --- */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            /* Ubah height menjadi calc agar pas 1 halaman dikurangi navbar */
            min-height: calc(100vh - 80px); 
            display: flex;
            align-items: center;
            color: white;
            /* Hapus margin negatif yang menyebabkan tumpukan */
            margin-top: 0; 
            padding-top: 0;
            position: relative;
        }

        .hero-title {
            font-weight: 800;
            font-size: 3.5rem;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .hero-title span {
            color: var(--primary-lime);
        }

        .btn-cta {
            background-color: var(--primary-lime);
            color: var(--dark-grey);
            font-weight: 800;
            padding: 15px 40px;
            border-radius: 50px;
            border: none;
            text-transform: uppercase;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(214, 233, 39, 0.4);
            transition: all 0.3s ease;
        }

        .btn-cta:hover {
            background-color: #cddc24;
            transform: translateY(-3px);
            color: black;
            box-shadow: 0 6px 20px rgba(214, 233, 39, 0.6);
        }

        /* --- Features/About --- */
        .section-padding {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-weight: 800;
            text-transform: uppercase;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: var(--primary-red);
            margin: 15px auto;
        }

        /* --- Class Cards & Slider Styles --- */
        .class-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            transition: 0.3s;
            height: 100%;
            background: white;
        }

        .class-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .class-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .class-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        .class-card:hover .class-img-wrapper img {
            transform: scale(1.1);
        }

        .class-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--primary-lime);
            color: var(--dark-grey);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .class-body {
            padding: 25px;
        }

        .class-title {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 10px;
        }

        .class-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .btn-book {
            width: 100%;
            background-color: var(--dark-grey);
            color: white;
            font-weight: 600;
            padding: 10px;
            border-radius: 8px;
            border: none;
        }

        .btn-book:hover {
            background-color: var(--primary-red);
            color: white;
        }

        /* --- Schedule Table --- */
        .schedule-section {
            background-color: #f4f4f4;
        }

        .table-custom th {
            background-color: var(--dark-grey);
            color: white;
            text-align: center;
            border: none;
            padding: 15px;
        }

        .table-custom td {
            text-align: center;
            vertical-align: middle;
            background-color: white;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            font-weight: 500;
        }

        .class-slot {
            background-color: rgba(214, 233, 39, 0.2); /* Light lime */
            color: #333;
            padding: 5px;
            border-radius: 5px;
            display: block;
            margin-bottom: 5px;
            font-weight: 700;
            border-left: 3px solid var(--primary-lime);
        }
        
        .class-slot.intense {
            background-color: rgba(230, 46, 45, 0.1); /* Light red */
            border-left: 3px solid var(--primary-red);
        }

        /* --- Shared Slider Styles (Trainers & Classes) --- */
        .slider-container-relative {
            position: relative;
            padding: 0 10px;
        }

        .scrolling-wrapper {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Penting untuk iOS agar smooth */
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory; /* FITUR BARU: Mengunci scroll horizontal */
            gap: 20px; /* Jarak antar kartu sedikit dirapatkan */
            padding: 20px 10px 40px 10px;
            
            /* Sembunyikan Scrollbar */
            scrollbar-width: none; 
            -ms-overflow-style: none; 
        }
        
        .scrolling-wrapper::-webkit-scrollbar { 
            display: none; 
        }

        /* Wrapper untuk Trainer */
        .trainer-card-wrapper {
            flex: 0 0 auto;
            width: 280px; 
            scroll-snap-align: center; /* FITUR BARU: Kartu akan berhenti di tengah layar */
        }
        
        /* Wrapper untuk Class (Lebih lebar sedikit dari trainer) */
        .class-card-wrapper {
            flex: 0 0 auto;
            width: 320px;
            scroll-snap-align: center; /* FITUR BARU: Kartu akan berhenti di tengah layar */
        }

        @media (max-width: 576px) {
            .class-card-wrapper, .trainer-card-wrapper {
                width: 85vw; /* Di mobile, lebar kartu mengikuti 85% lebar layar agar sisi kartu lain terlihat */
            }
            
            .scrolling-wrapper {
                padding-left: 20px;
                padding-right: 20px;
                scroll-padding-left: 20px; /* Offset snap point */
            }
            
            .class-card-wrapper, .trainer-card-wrapper {
                scroll-snap-align: center;
                width: 280px; /* Kembalikan ke fixed width agar tidak terlalu lebar */
            }
            
            .hero-title {
                font-size: 2.5rem; /* Perkecil font di mobile */
            }
        }

        /* Override media query agar lebih rapi di mobile */
        @media (max-width: 576px) {
             .class-card-wrapper, .trainer-card-wrapper {
                width: 260px; /* Sedikit lebih kecil agar user tahu ada konten di sebelahnya */
                scroll-snap-align: center;
            }
        }

        .trainer-card {
            border: none;
            background: white; 
            text-align: center;
            padding: 30px 20px;
            transition: 0.3s;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
        }

        .trainer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .trainer-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--bg-dark);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: 0.3s;
        }

        .trainer-card:hover .trainer-img {
            border-color: var(--primary-lime);
        }

        .trainer-name {
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .trainer-role {
            color: var(--primary-red);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        /* Tombol Navigasi Slider */
        .scroll-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--dark-grey);
            cursor: pointer;
            transition: 0.3s;
        }
        
        .scroll-btn:hover {
            background-color: var(--primary-lime);
            color: black;
        }
        
        .scroll-left { left: -10px; }
        .scroll-right { right: -10px; }
        
        /* Hide buttons on mobile since they can swipe */
        @media (max-width: 768px) {
            .scroll-btn { display: none; }
        }

        /* --- Footer --- */
        footer {
            background-color: var(--bg-dark);
            color: #aaa;
            padding: 60px 0 20px;
        }

        footer h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .social-icons a {
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: 0.3s;
        }

        .social-icons a:hover {
            color: var(--primary-lime);
        }

        /* --- Google Login Btn Style --- */
        .google-btn {
            width: 100%;
            background-color: white;
            border: 1px solid #ddd;
            color: #555;
            padding: 10px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 15px;
            transition: 0.2s;
            cursor: pointer;
        }
        
        .google-btn:hover {
            background-color: #f1f1f1;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .google-btn img {
            width: 20px;
            margin-right: 10px;
        }
        .schedule-pill{
        display:inline-block;
        min-width: 140px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f7f7f7;
        border-left: 4px solid #bbb;
        }

        .pill-past{
        background: #fdecec;
        border-left-color: #ff3b3b;
        }

        .pill-soon{
        background: #f3f9da;
        border-left-color: #b7d600;
        }

        /* opsional: lusa dst netral */
        .pill-future{
        background: #f4f4f4;
        border-left-color: #cfcfcf;
        }

        .pill-off{
        background: #f1f1f1;
        border-left-color: #777;
        opacity: .85;
        }

        .schedule-meta {
        margin-top: 6px;
        font-size: 12px;
        line-height: 1.35;
        color: #555;
        }

        .schedule-meta i {
        width: 14px;
        color: var(--primary-red);
        }

    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <!-- Menggunakan logo yang diupload user -->
                <img src="{{ asset('storage/' . $branchStore->logo) }}" alt="{{ $branchStore->name }}" style="border-radius: 4px;">
            </a>          
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    {{-- <li class="nav-item"><a class="nav-link" href="#classes">Kelas</a></li> --}}
                    <li class="nav-item"><a class="nav-link" href="#schedule">Jadwal</a></li>
                    {{-- <li class="nav-item"><a class="nav-link" href="#trainers">Trainer</a></li> --}}
                    <li class="nav-item" id="authContainer">
                    @if(auth('member')->check())
                        @php $m = auth('member')->user(); @endphp
                        <div class="dropdown">
                            <button class="btn btn-dark dropdown-toggle rounded-pill px-4" type="button" data-bs-toggle="dropdown">
                                <img src="{{ $m->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($m->full_name) }}" class="rounded-circle me-2" style="width:25px;">
                                {{ $m->full_name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                {{-- <li><hr class="dropdown-divider"></li> --}}
                                <li>
                                    <form method="POST" action="{{ route('member.logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <button class="btn btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="fa-solid fa-user me-2"></i> Member Login
                        </button>
                    @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container text-center">
            <h1 class="hero-title">Unlock Your<br><span>True Potential</span></h1>
            <p class="lead mb-4 text-white-50">Bergabung dengan komunitas {{ $branchStore->name }} terbaik. Latihan keras, hasil nyata.</p>
            <a href="#schedule" class="btn btn-cta">Booking Kelas Sekarang</a>
        </div>
    </section>

    <!-- Kelas Section (Updated Swipe Scroll) -->
    {{-- <section id="classes" class="section-padding">
        <div class="container">
            <h2 class="section-title">Kelas Unggulan</h2>
            <p class="text-center mb-5">Pilih latihan yang sesuai dengan target kebugaranmu. Geser untuk melihat lebih banyak.</p>

            <div class="slider-container-relative">
                <!-- Tombol Navigasi Kiri -->
                <button class="scroll-btn scroll-left" onclick="scrollContainer('classScroller', 'left')">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <!-- Container Geser -->
                <div class="scrolling-wrapper" id="classScroller">
                    
                    <!-- Card 1: Tinju -->
                    <div class="class-card-wrapper">
                        <div class="card class-card">
                            <div class="class-img-wrapper">
                                <span class="class-badge">High Intensity</span>
                                <img src="https://images.unsplash.com/photo-1599058945522-28d584b6f0ff?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Boxing">
                            </div>
                            <div class="class-body">
                                <h3 class="class-title">Boxing (Tinju)</h3>
                                <div class="class-meta">
                                    <i class="fa-regular fa-clock me-2"></i> 60 Menit &bull; 
                                    <i class="fa-solid fa-fire ms-2 me-2"></i> 800 Kalori
                                </div>
                                <p class="text-muted">Latihan kardio dan kekuatan eksplosif. Tingkatkan refleks dan stamina Anda.</p>
                                <button class="btn btn-book" onclick="bookClass('Boxing')">Book Now</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Senam / Aerobik -->
                    <div class="class-card-wrapper">
                        <div class="card class-card">
                            <div class="class-img-wrapper">
                                <span class="class-badge">Cardio</span>
                                <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Aerobics">
                            </div>
                            <div class="class-body">
                                <h3 class="class-title">Aerobik & Zumba</h3>
                                <div class="class-meta">
                                    <i class="fa-regular fa-clock me-2"></i> 45 Menit &bull; 
                                    <i class="fa-solid fa-fire ms-2 me-2"></i> 500 Kalori
                                </div>
                                <p class="text-muted">Gerakan ritmis yang menyenangkan untuk membakar lemak dan meningkatkan mood.</p>
                                <button class="btn btn-book" onclick="bookClass('Aerobik')">Book Now</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Body Pump -->
                    <div class="class-card-wrapper">
                        <div class="card class-card">
                            <div class="class-img-wrapper">
                                <span class="class-badge">Strength</span>
                                <img src="https://images.unsplash.com/photo-1534367507873-d2d7e24c797f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Weight Lifting">
                            </div>
                            <div class="class-body">
                                <h3 class="class-title">Body Pump</h3>
                                <div class="class-meta">
                                    <i class="fa-regular fa-clock me-2"></i> 60 Menit &bull; 
                                    <i class="fa-solid fa-fire ms-2 me-2"></i> 600 Kalori
                                </div>
                                <p class="text-muted">Latihan angkat beban seluruh tubuh untuk membentuk otot dan kekuatan.</p>
                                <button class="btn btn-book" onclick="bookClass('Body Pump')">Book Now</button>
                            </div>
                        </div>
                    </div>

                     <!-- Card 4: Yoga (Tambahan untuk demo scroll) -->
                     <div class="class-card-wrapper">
                        <div class="card class-card">
                            <div class="class-img-wrapper">
                                <span class="class-badge" style="background-color: #A8D5BA;">Flexibility</span>
                                <img src="https://images.unsplash.com/photo-1544367563-12123d8965cd?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Yoga">
                            </div>
                            <div class="class-body">
                                <h3 class="class-title">Hatha Yoga</h3>
                                <div class="class-meta">
                                    <i class="fa-regular fa-clock me-2"></i> 60 Menit &bull; 
                                    <i class="fa-solid fa-fire ms-2 me-2"></i> 300 Kalori
                                </div>
                                <p class="text-muted">Fokus pada pernapasan, ketenangan, dan fleksibilitas tubuh.</p>
                                <button class="btn btn-book" onclick="bookClass('Hatha Yoga')">Book Now</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5: CrossFit (Tambahan) -->
                    <div class="class-card-wrapper">
                        <div class="card class-card">
                            <div class="class-img-wrapper">
                                <span class="class-badge" style="background-color: var(--primary-red); color: white;">Hardcore</span>
                                <img src="https://images.unsplash.com/photo-1517963879433-6ad2b056d712?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Crossfit">
                            </div>
                            <div class="class-body">
                                <h3 class="class-title">CrossFit</h3>
                                <div class="class-meta">
                                    <i class="fa-regular fa-clock me-2"></i> 50 Menit &bull; 
                                    <i class="fa-solid fa-fire ms-2 me-2"></i> 900 Kalori
                                </div>
                                <p class="text-muted">Latihan fungsional intensitas tinggi untuk performa atletik maksimal.</p>
                                <button class="btn btn-book" onclick="bookClass('CrossFit')">Book Now</button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Navigasi Kanan -->
                <button class="scroll-btn scroll-right" onclick="scrollContainer('classScroller', 'right')">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section> --}}

    <!-- Schedule Section -->
    <section id="schedule" class="section-padding schedule-section">
        <div class="container">
            <h2 class="section-title">Jadwal Minggu Ini</h2>
            <div class="container mt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>            
            <div class="table-responsive">
                <table class="table table-custom table-bordered">
                    <thead>
                        <tr>
                            <th width="10%">Jam</th>
                            @foreach($days as $dayNum => $dayName)
                            <th>
                            {{ $dayName }}
                            <div style="font-size:12px; opacity:.7">
                                {{ $weekDates[$dayNum]->format('d/m') }}
                            </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($timeSlots as $time)
                    <tr>
                        <td class="fw-semibold">{{ $time }}</td>

                        @foreach($days as $dayNum => $dayName)
                            @php
                                $cellDate = $weekDates[$dayNum]->copy()->startOfDay();
                                $today = now()->copy()->startOfDay();
                                $tomorrow = now()->copy()->addDay()->startOfDay();

                                $isSoon = $cellDate->equalTo($today) || $cellDate->equalTo($tomorrow);

                                $items = $scheduleGrid[$time][$dayNum] ?? [];
                            @endphp

                        <td>
                            @forelse($items as $sc)
                                @php
                                    // waktu kelas
                                    $classStart = \Carbon\Carbon::parse($sc->class_date.' '.$sc->time_start);
                                    $classEnd   = \Carbon\Carbon::parse($sc->class_date.' '.$sc->time_end);

                                    // batas booking: maksimal 1 jam sebelum mulai
                                    $bookingDeadline = $classStart->copy()->subHour();

                                    // status waktu
                                    $hasClassStarted = now()->gte($classStart);
                                    $canBookNow = now()->lt($bookingDeadline); // masih sebelum deadline

                                    $isClassOff = ! (bool) $sc->is_active || ! (bool) ($sc->classSession?->is_active ?? true);
                                    $pillClass = $isClassOff ? 'pill-off' : ($hasClassStarted ? 'pill-past' : ($isSoon ? 'pill-soon' : 'pill-future'));
                                    $capacity = (int) $sc->capacity;
                                    $bookedCount = (int) ($sc->booked_count ?? 0);
                                    $remainingQuota = max($capacity - $bookedCount, 0);
                                @endphp

                                <div class="schedule-pill {{ $pillClass }} mb-2">
                                    <div class="fw-bold">{{ $sc->name }}</div>

                                    <small class="text-muted">
                                        • {{ \Carbon\Carbon::parse($sc->time_start)->format('H:i') }}–{{ \Carbon\Carbon::parse($sc->time_end)->format('H:i') }}
                                    </small>
                                    <div class="schedule-meta">
                                        <div>
                                            <i class="fa-solid fa-user-tie me-1"></i>
                                            {{ $sc->classInstructor?->full_name ?? 'Trainer belum ditentukan' }}
                                        </div>
                                        <div>
                                            <i class="fa-solid fa-ticket me-1"></i>
                                            @if($hasClassStarted)
                                                Total quota: {{ $capacity }}
                                            @else
                                                Sisa {{ $remainingQuota }} dari {{ $capacity }} quota
                                            @endif
                                        </div>
                                    </div>

                                    @if($isClassOff)
                                        <div class="mt-2 text-muted small">
                                            Kelas libur (tidak bisa dibooking)
                                        </div>

                                    @elseif($hasClassStarted)
                                        <div class="mt-2 text-muted small">
                                            Selesai
                                        </div>

                                    {{-- Tombol hanya muncul untuk Today/Tomorrow + masih sebelum deadline --}}
                                    @elseif($isSoon && $canBookNow)
                                        @if(auth('member')->check())
                                            @php
                                                $isBooked = \App\Models\ClassDetail::where('class_schedule_id', $sc->id)
                                                    ->where('member_id', auth('member')->id())
                                                    ->whereNull('canceled_at')
                                                    ->exists();
                                                $cancelDeadline = $classStart->copy()->subHours(4);
                                                $canCancel = now()->lt($cancelDeadline);
                                            @endphp

                                            @if($isBooked)
                                                @if($canCancel)
                                                    <form method="POST" action="{{ route('booking.cancel') }}" class="mt-2">
                                                        @csrf
                                                        <input type="hidden" name="class_schedule_id" value="{{ $sc->id }}">
                                                        <button class="btn btn-sm btn-outline-danger w-100" type="submit">Cancel</button>
                                                    </form>
                                                @else
                                                    <div class="mt-2 text-muted small">
                                                        Tidak bisa cancel (< 4 jam)
                                                    </div>
                                                @endif
                                            @else
                                                <form method="POST" action="{{ route('booking.store') }}" class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="class_schedule_id" value="{{ $sc->id }}">
                                                    <button class="btn btn-sm btn-dark w-100" type="submit">Book</button>
                                                </form>
                                            @endif
                                        @else
                                            <button class="btn btn-sm btn-dark w-100 mt-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                Login untuk booking
                                            </button>
                                        @endif

                                    {{-- Kalau sudah mendekati kelas (kurang dari 1 jam) / sudah mulai --}}
                                    @elseif($isSoon && !$canBookNow)
                                        <div class="mt-2 text-muted small">
                                            Closed (maks. booking 1 jam sebelum)
                                        </div>

                                    @else
                                        <div class="mt-2 text-muted small">
                                            Booking dibuka H-1
                                        </div>
                                    @endif
                                </div>
                            @empty
                                {{-- kosong --}}
                            @endforelse
                        </td>

                        @endforeach
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Trainer Section (UPDATED SLIDER) -->
    {{-- <section id="trainers" class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title">Trainer Profesional</h2>
            <p class="text-center mb-5">Geser untuk melihat tim ahli kami yang siap membantu Anda.</p>

            <div class="slider-container-relative">
                <!-- Tombol Navigasi Kiri -->
                <button class="scroll-btn scroll-left" onclick="scrollContainer('trainerScroller', 'left')">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>

                <!-- Container Geser -->
                <div class="scrolling-wrapper" id="trainerScroller">
                    
                    <!-- Trainer 1 -->
                    <div class="trainer-card-wrapper">
                        <div class="trainer-card">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Trainer 1" class="trainer-img">
                            <h4 class="trainer-name">Rico Wijaya</h4>
                            <div class="trainer-role">Head Coach & Boxing</div>
                            <p class="text-muted small">Mantan atlet nasional dengan pengalaman 10 tahun melatih teknik tinju profesional.</p>
                            <div class="social-icons mt-auto">
                                <a href="#" class="text-dark"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" class="text-dark"><i class="fa-brands fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Trainer 2 -->
                    <div class="trainer-card-wrapper">
                        <div class="trainer-card">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Trainer 2" class="trainer-img">
                            <h4 class="trainer-name">Sinta Bella</h4>
                            <div class="trainer-role">Yoga & Pilates</div>
                            <p class="text-muted small">Bersertifikat internasional dalam Hatha Yoga. Fokus pada fleksibilitas.</p>
                            <div class="social-icons mt-auto">
                                <a href="#" class="text-dark"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" class="text-dark"><i class="fa-brands fa-facebook"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Trainer 3 -->
                    <div class="trainer-card-wrapper">
                        <div class="trainer-card">
                            <img src="https://randomuser.me/api/portraits/men/85.jpg" alt="Trainer 3" class="trainer-img">
                            <h4 class="trainer-name">Doni "The Rock"</h4>
                            <div class="trainer-role">Strength</div>
                            <p class="text-muted small">Spesialis pembentukan otot dan kekuatan. Metode latihan teruji.</p>
                            <div class="social-icons mt-auto">
                                <a href="#" class="text-dark"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" class="text-dark"><i class="fa-brands fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trainer 4 -->
                    <div class="trainer-card-wrapper">
                        <div class="trainer-card">
                            <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Trainer 4" class="trainer-img">
                            <h4 class="trainer-name">Maya Aerobik</h4>
                            <div class="trainer-role">Zumba & Cardio</div>
                            <p class="text-muted small">Instruktur Zumba berlisensi yang energik. Kelasnya selalu penuh semangat.</p>
                            <div class="social-icons mt-auto">
                                <a href="#" class="text-dark"><i class="fa-brands fa-instagram"></i></a>
                                <a href="#" class="text-dark"><i class="fa-brands fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Trainer 5 (Extra) -->
                    <div class="trainer-card-wrapper">
                        <div class="trainer-card">
                            <img src="https://randomuser.me/api/portraits/men/22.jpg" alt="Trainer 5" class="trainer-img">
                            <h4 class="trainer-name">Eko Prasetyo</h4>
                            <div class="trainer-role">Crossfit</div>
                            <p class="text-muted small">Pelatih fisik intensitas tinggi untuk performa maksimal.</p>
                            <div class="social-icons mt-auto">
                                <a href="#" class="text-dark"><i class="fa-brands fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Navigasi Kanan -->
                <button class="scroll-btn scroll-right" onclick="scrollContainer('trainerScroller', 'right')">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section> --}}

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                {{-- <div class="col-md-4 mb-4">
                    <img src="image_575d23.jpg" alt="Logo Footer" style="height: 40px; margin-bottom: 20px; border-radius:4px;">
                    <p>LevelFit Wellness Community adalah tempat di mana kebugaran bertemu dengan komunitas. Kami menyediakan fasilitas terbaik dan pelatih profesional untuk mencapai target Anda.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none text-muted">Tentang Kami</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Karir</a></li>
                    </ul>
                </div> --}}
                <div class="col-md-12 mb-4">
                    <h5>{{ $branchStore->name }}</h5>
                    <p><i class="fa-solid fa-location-dot me-2"></i> {{ $branchStore->address }} {{ $branchStore->city }}</p>
                    <p><i class="fa-solid fa-phone me-2"></i> {{ $branchStore->phone }}</p>
                    {{-- <div class="social-icons mt-3">
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
                    </div> --}}
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <div class="text-center pt-2">
                <small>&copy; 2026 LEVELFIT. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Login Modal (Simulasi Google Login) -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Masuk Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="text-muted mb-4">Pastikan email Google anda terdaftar di Administrator</p>
                    
                    <!-- Tombol Google Login -->
                    <a class="google-btn text-decoration-none" href="{{ route('member.google.redirect') }}">
                        <img src="{{ asset('google.png') }}" alt="Google Logo">
                        Sign in with Google
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Confirmation Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-check-circle me-2"></i>Booking Berhasil!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <h4 class="mb-3" id="bookedClassName">Nama Kelas</h4>
                    <p>Slot anda telah diamankan. Silahkan datang 15 menit sebelum kelas dimulai untuk persiapan.</p>
                    <div class="alert alert-light border mt-3">
                        <strong>Kode Booking:</strong> <span class="text-primary font-monospace">LVL-8829</span>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Logic Script -->
    <script>
        // Fungsi Slider Universal (Bisa untuk Class & Trainer)
        function scrollContainer(containerId, direction) {
            const container = document.getElementById(containerId);
            const scrollAmount = 340; // Sesuaikan jarak geser (lebar card + gap)

            if (direction === 'left') {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }

        // Fungsi Booking Kelas
        function bookClass(className) {
            if (!isLoggedIn) {
                // Jika belum login, tampilkan modal login
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
                
                // Tambahkan pesan kecil di alert (opsional)
                // alert("Silahkan login dengan Google terlebih dahulu untuk booking kelas.");
            } else {
                // Jika sudah login, tampilkan konfirmasi sukses
                document.getElementById('bookedClassName').textContent = className;
                const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                bookingModal.show();
            }
        }
    </script>

</body>
</html>
