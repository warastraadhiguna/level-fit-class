<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Pilih Cabang</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $branchStores[0]->logo) }}">        
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #ff3b3b;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
            --text-light: #f5f5f5;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-light);
            min-height: 100vh; 
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            width: calc(100% - 32px);
            max-width: 1680px;
        }

        /* Background Image */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            filter: brightness(0.3);
            z-index: -1;
        }

        .main-header {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
            margin-top: 1rem;
            position: relative;
            z-index: 2;
        }

        .gym-logo {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .header-title {
            font-weight: 700;
            font-size: 3.5rem;
        }

        @media (max-width: 992px) {
            .header-title { font-size: 2.5rem; }
            .gym-logo { font-size: 2.5rem; }
        }

        @media (max-width: 576px) {
            .header-title { font-size: 2rem; }
        }

        /* Container Horizontal Scroll */
        .slider-container {
            position: relative;
            width: 100%;
            max-width: 1680px;
            padding: 0 16px; 
            margin-bottom: 2rem;
            z-index: 2;
        }

        /* === BAGIAN PENTING YANG MEMPERBAIKI POSISI === */
        .cards-wrapper {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding: 20px 10px 40px 10px;
            gap: 20px;
            scrollbar-width: none; 

            width: 100%;
            justify-content: center;
        }

        .cards-wrapper::-webkit-scrollbar {
            display: none;
        }

        .card-item {
            flex: 0 0 300px; /* Lebar tetap kartu */
            scroll-snap-align: center;
            perspective: 1000px;
        }

        @media (max-width: 768px) {
            .card-item { flex: 0 0 85vw; }
            .slider-container { padding: 0; }
            .cards-wrapper {
                justify-content: flex-start;
                padding: 20px 20px 40px 20px;
            }
        }

        .choice-card {
            background: rgba(30, 30, 30, 0.85);
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .choice-card:hover {
            transform: translateY(-10px);
            border-color: var(--primary-color);
            box-shadow: 0 0 30px rgba(255, 59, 59, 0.3);
            background: rgba(30, 30, 30, 0.95);
        }

        .choice-card h2 {
            font-family: 'Oswald', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            margin-top: 1rem;
        }

        .choice-card p {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .branch-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
            /* Tambahkan background jika gambar error/kosong */
            background-color: #333; 
            filter: grayscale(80%);
            transition: 0.4s;
        }

        .choice-card:hover .branch-img {
            filter: grayscale(0%);
        }

        .btn-visit {
            background-color: transparent;
            border: 2px solid var(--primary-color);
            color: var(--text-light);
            padding: 8px 25px;
            border-radius: 30px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: 0.3s;
            white-space: nowrap;
        }

        .choice-card:hover .btn-visit {
            background-color: var(--primary-color);
            color: white;
        }

        /* Navigation Arrows */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 59, 59, 0.8);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }

        .nav-btn:hover {
            background: #ff3b3b;
            transform: translateY(-50%) scale(1.1);
        }

        .prev-btn { left: 0; }
        .next-btn { right: 0; }

        @media (max-width: 768px) {
            .nav-btn { display: none; }
        }

        /* Footer */
        footer {
            margin-top: auto; 
            padding: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: #888;
            position: relative;
            z-index: 2;
        }
        
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .branch-meta{
        width: 100%;
        text-align: left;       /* biar rapi untuk info */
        margin-bottom: 1.5rem;  /* menggantikan margin p sebelumnya */
        }

        .meta-row{
        display: flex;
        align-items: flex-start; /* icon tetap di atas saat address wrap */
        gap: 8px;
        margin-bottom: 6px;
        }

        .meta-text{
        flex: 1;
        word-break: break-word; /* kalau ada kata panjang / nomor */
        white-space: normal;    /* pastikan bisa turun baris */
        line-height: 1.25rem;
        color: #aaa;            /* mirip style p kamu */
        font-size: 0.9rem;
        }

    </style>
</head>
<body>

    <div class="bg-container"></div>

    <div class="main-wrapper">
        
        <div class="container text-center main-header">
            <h1 class="header-title">{{ config('app.name') }}</h1>
            <p class="lead text-light">Pilih lokasi gym terdekat Anda</p>
        </div>

            @if (session('auth_error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Login gagal.</strong> {{ session('auth_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif  
        <div class="slider-container">
            <button class="nav-btn prev-btn" onclick="scrollSlider(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next-btn" onclick="scrollSlider(1)"><i class="fas fa-chevron-right"></i></button>

            <!-- Pastikan Loop PHP Anda berada DI DALAM div cards-wrapper ini -->
            <div class="cards-wrapper" id="cardSlider">
                
                <!-- CONTOH SIMULASI HASIL RENDER PHP (Untuk Preview) -->
                <!-- Ini mensimulasikan jika database Anda mengembalikan 2 cabang -->
                @foreach($branchStores as $branchStore)
                    <div class="card-item">
                        <div class="choice-card" onclick="visitGym('{{ $branchStore->slug }}')">                            
                            <h2>{{ $branchStore->name }}</h2>
                            <div class="branch-meta">
                                <div class="meta-row">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <span class="meta-text">{{ $branchStore->address }} {{ $branchStore->city }}</span>
                                </div>

                                <div class="meta-row">
                                    <i class="fas fa-phone text-danger me-2"></i>
                                    <span class="meta-text">{{ $branchStore->phone }}</span>
                                </div>
                            </div>                        
                            <button class="btn btn-visit">Masuk Web</button>
                        </div>
                    </div>                                        
                @endforeach
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 {{ config('app.name') }}. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function visitGym(url) {
            const card = event.currentTarget;
            card.style.transform = "scale(0.95)";
            setTimeout(() => {
                // Pastikan URL valid, jika slug hanya teks, tambahkan base url
                // Contoh: window.open('/cabang/' + url, '_blank');
                window.open(url, '_blank');
                card.style.transform = "";
            }, 150);
        }

        const slider = document.getElementById('cardSlider');
        function scrollSlider(direction) {
            const scrollAmount = 320; 
            if (direction === 1) {
                slider.scrollLeft += scrollAmount;
            } else {
                slider.scrollLeft -= scrollAmount;
            }
        }

        document.addEventListener('mousemove', (e) => {
            const bg = document.querySelector('.bg-container');
            const x = (window.innerWidth - e.pageX * 2) / 100;
            const y = (window.innerHeight - e.pageY * 2) / 100;
            bg.style.transform = `translateX(${x}px) translateY(${y}px)`;
        });
    </script>
</body>
</html>
