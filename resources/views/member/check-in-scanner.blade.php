<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { min-height: 100vh; background: #121212; color: #f5f5f5; }
        .scanner-shell { max-width: 620px; margin: 0 auto; padding: 32px 16px; }
        .scanner-card { background: #1e1e1e; border: 1px solid #333; border-radius: 18px; padding: 24px; }
        #qr-reader { overflow: hidden; border-radius: 12px; background: #000; }
        #qr-reader video { border-radius: 12px; }
        .btn-level-fit { background: #d6e927; color: #222; font-weight: 700; }
        .member-chip { color: #bbb; font-size: .9rem; }
    </style>
</head>
<body>
    <main class="scanner-shell">
        <a href="{{ $returnUrl }}" class="btn btn-sm btn-outline-light mb-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>

        <div class="scanner-card shadow-lg">
            <div class="text-center mb-4">
                <i class="fa-solid fa-qrcode fa-3x text-danger mb-3"></i>
                <h1 class="h3">{{ $heading }}</h1>
                <p class="member-chip mb-0">{{ auth('member')->user()->full_name }}</p>
                <p class="text-secondary">{{ $description }}</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success text-center">
                    <strong>{{ session('check_in_status') === 'checked_out' ? 'Check Out Berhasil' : 'Check In Berhasil' }}</strong><br>
                    {{ session('success') }}
                    @if (session('trainer_session'))
                        <div class="small mt-2">
                            {{ session('trainer_session.package_name') }} · {{ session('trainer_session.trainer_name') }}
                        </div>
                    @endif
                </div>
            @endif
            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            @if (session('error') || $errors->any())
                <div class="alert alert-danger">{{ session('error') ?: $errors->first() }}</div>
            @endif

            <div id="qr-reader"></div>
            <p id="scanner-status" class="text-center text-secondary mt-3 mb-2">Tekan tombol untuk mengaktifkan kamera.</p>

            <div class="d-grid gap-2">
                <button type="button" id="start-scanner" class="btn btn-level-fit">
                    <i class="fa-solid fa-camera me-2"></i>Mulai Scan
                </button>
                <button type="button" id="stop-scanner" class="btn btn-outline-light d-none">Hentikan Kamera</button>
            </div>

            <form id="qr-check-in-form" method="POST" action="{{ $formAction }}" class="d-none">
                @csrf
                <input type="hidden" name="qr_payload" id="qr-payload">
            </form>

            <hr class="border-secondary my-4">
            <label for="qr-file" class="form-label text-secondary">Kamera bermasalah? Pilih foto QR dari galeri:</label>
            <input id="qr-file" class="form-control" type="file" accept="image/*">
        </div>
    </main>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const scanner = new Html5Qrcode('qr-reader');
        const startButton = document.getElementById('start-scanner');
        const stopButton = document.getElementById('stop-scanner');
        const statusText = document.getElementById('scanner-status');
        const fileInput = document.getElementById('qr-file');
        let isRunning = false;
        let isSubmitting = false;
        const expectedQrPrefix = @json($qrPrefix);

        function submitQr(decodedText) {
            if (isSubmitting) return;
            if (!decodedText.startsWith(expectedQrPrefix)) {
                statusText.textContent = 'Jenis QR code tidak sesuai dengan halaman scanner ini.';
                statusText.className = 'text-center text-danger mt-3 mb-2';
                return;
            }

            isSubmitting = true;
            statusText.textContent = 'QR ditemukan. Memproses...';
            statusText.className = 'text-center text-success mt-3 mb-2';
            document.getElementById('qr-payload').value = decodedText;

            const submit = () => document.getElementById('qr-check-in-form').submit();
            if (isRunning) scanner.stop().then(submit).catch(submit);
            else submit();
        }

        async function startScanner() {
            startButton.disabled = true;
            statusText.textContent = 'Meminta izin kamera...';

            try {
                await scanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    submitQr,
                    function () {}
                );
                isRunning = true;
                startButton.classList.add('d-none');
                stopButton.classList.remove('d-none');
                statusText.textContent = 'Kamera aktif. Arahkan ke QR code.';
            } catch (error) {
                statusText.textContent = 'Kamera tidak dapat dibuka. Pastikan izin kamera aktif atau gunakan foto dari galeri.';
                statusText.className = 'text-center text-danger mt-3 mb-2';
                startButton.disabled = false;
            }
        }

        async function stopScanner() {
            if (!isRunning) return;
            await scanner.stop();
            isRunning = false;
            startButton.disabled = false;
            startButton.classList.remove('d-none');
            stopButton.classList.add('d-none');
            statusText.textContent = 'Kamera dihentikan.';
        }

        startButton.addEventListener('click', startScanner);
        stopButton.addEventListener('click', stopScanner);
        fileInput.addEventListener('change', async function(event) {
            const file = event.target.files[0];
            if (!file) return;

            try {
                if (isRunning) await stopScanner();
                submitQr(await scanner.scanFile(file, true));
            } catch (error) {
                statusText.textContent = 'QR code tidak ditemukan pada foto tersebut.';
                statusText.className = 'text-center text-danger mt-3 mb-2';
            }
        });
    </script>
</body>
</html>
