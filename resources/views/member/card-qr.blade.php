<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Card Member - Level FIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { min-height: 100vh; background: #121212; color: #f5f5f5; }
        .qr-shell { max-width: 520px; margin: 0 auto; padding: 32px 16px; }
        .qr-card { background: #1e1e1e; border: 1px solid #333; border-radius: 18px; padding: 28px; }
        .qr-box { display: inline-block; padding: 16px; background: #fff; border-radius: 14px; }
        .member-chip { color: #bbb; font-size: .9rem; }
        .card-number { letter-spacing: 2px; font-weight: 700; color: #d6e927; }
    </style>
</head>
<body>
    <main class="qr-shell">
        <a href="{{ $returnUrl }}" class="btn btn-sm btn-outline-light mb-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>

        <div class="qr-card shadow-lg text-center">
            <i class="fa-solid fa-id-card fa-3x text-danger mb-3"></i>
            <h1 class="h3">QR Card Member</h1>
            <p class="member-chip mb-1">{{ $member->full_name }}</p>

            @if ($member->card_number)
                <p class="text-secondary">Tunjukkan QR ini kepada petugas untuk membership atau trainer session check-in/out.</p>
                <div class="qr-box my-3">
                    <div id="memberCardQr"></div>
                </div>
                <div class="card-number">{{ $member->card_number }}</div>
                <p class="text-secondary small mt-3 mb-0">QR ini berisi card number member Anda.</p>
            @else
                <div class="alert alert-warning mt-4 mb-0">
                    Card number belum tersedia. Silakan hubungi resepsionis Level FIT.
                </div>
            @endif
        </div>
    </main>

    @if ($member->card_number)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            new QRCode(document.getElementById('memberCardQr'), {
                text: @json((string) $member->card_number),
                width: 260,
                height: 260,
                correctLevel: QRCode.CorrectLevel.M
            });
        </script>
    @endif
</body>
</html>
