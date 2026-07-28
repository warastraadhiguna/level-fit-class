<main class="page">
    <div style="display:flex; justify-content:space-between; gap:16px; align-items:flex-start; margin-bottom:18px;">
        <div>
            <h1 style="font-size:30px; line-height:1.2; margin:0 0 6px;">Check In Kelas</h1>
            <div class="muted">Hanya untuk class details hari ini: {{ $todayLabel }}</div>
        </div>
        <a class="btn" style="text-decoration:none;" href="{{ url('/admin/class-details') }}">Lihat Class Details</a>
    </div>

    <section class="card" style="padding:20px; margin-bottom:18px;">
        <form id="admin-class-check-in-form" wire:submit="checkIn" style="display:grid; grid-template-columns: minmax(240px, 1fr) auto auto; gap:12px; align-items:end;">
            <div>
                <label for="cardNumber" style="display:block; font-size:13px; font-weight:700; margin-bottom:8px;">Card Number</label>
                <input
                    id="cardNumber"
                    class="input"
                    type="text"
                    wire:model="cardNumber"
                    placeholder="Scan atau ketik card number"
                    autocomplete="off"
                    autofocus
                >
            </div>
            <button class="btn btn-success btn-check-in" type="submit" wire:loading.attr="disabled">
                Check In
            </button>
            <button class="btn" id="open-admin-class-qr-scanner" type="button">
                Scan QR
            </button>
        </form>

        @if($message)
            <div
                style="
                    margin-top:14px;
                    padding:12px 14px;
                    border-radius:6px;
                    font-weight:700;
                    color: {{ $messageType === 'success' ? '#166534' : ($messageType === 'error' ? '#991b1b' : '#1f2937') }};
                    background: {{ $messageType === 'success' ? '#dcfce7' : ($messageType === 'error' ? '#fee2e2' : '#f3f4f6') }};
                    border: 1px solid {{ $messageType === 'success' ? '#86efac' : ($messageType === 'error' ? '#fecaca' : '#e5e7eb') }};
                "
            >
                {{ $message }}
            </div>
        @endif
    </section>

    <dialog id="admin-class-qr-dialog" class="qr-scanner-dialog">
        <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:16px;">
            <div>
                <div style="font-size:20px; font-weight:800;">Scan QR Card Member</div>
                <div class="muted" style="font-size:13px;">QR berisi card number member</div>
            </div>
            <button id="close-admin-class-qr-scanner" type="button" class="btn btn-danger btn-small">Tutup</button>
        </div>
        <div id="admin-class-qr-reader" wire:ignore style="width:min(420px, 82vw);"></div>
        <div id="admin-class-qr-status" class="muted" style="margin-top:12px; text-align:center;">
            Tekan Mulai Scan untuk mengaktifkan kamera.
        </div>
        <div style="display:flex; justify-content:center; gap:8px; margin-top:14px;">
            <button id="start-admin-class-qr-scanner" type="button" class="btn btn-success">Mulai Scan</button>
            <button id="stop-admin-class-qr-scanner" type="button" class="btn" hidden>Hentikan Kamera</button>
        </div>
        <div style="margin-top:14px;">
            <label for="admin-class-qr-image" class="muted" style="display:block; font-size:13px; margin-bottom:6px;">Atau pilih gambar QR</label>
            <input id="admin-class-qr-image" class="input" type="file" accept="image/*">
        </div>
    </dialog>

    <section style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:12px; margin-bottom:18px;">
        <div class="card" style="padding:16px;">
            <div class="muted" style="font-size:13px;">Total Booking Hari Ini</div>
            <div style="font-size:28px; font-weight:800;">{{ $summary['total'] }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div class="muted" style="font-size:13px;">Sudah Check In</div>
            <div style="font-size:28px; font-weight:800; color:var(--success);">{{ $summary['present'] }}</div>
        </div>
        <div class="card" style="padding:16px;">
            <div class="muted" style="font-size:13px;">Belum Berangkat</div>
            <div style="font-size:28px; font-weight:800; color:var(--warning);">{{ $summary['absent'] }}</div>
        </div>
    </section>

    <section style="margin-bottom:18px;">
        <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:10px;">
            <div style="font-weight:800;">Ringkasan Per Class</div>
            <div class="muted" style="font-size:13px;">Total booking, sudah check in, dan belum berangkat hari ini</div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:12px;">
            @forelse($classSummaries as $classSummary)
                <div class="card" wire:key="class-summary-{{ $classSummary['key'] }}" style="padding:16px;">
                    <div style="font-weight:800; margin-bottom:3px;">{{ $classSummary['name'] }}</div>
                    <div class="muted" style="font-size:12px; margin-bottom:12px;">
                        {{ $classSummary['time_start'] }} - {{ $classSummary['time_end'] }} · {{ $classSummary['branch'] }}
                    </div>

                    <div style="display:grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap:10px;">
                        <div>
                            <div class="muted" style="font-size:12px;">Booking</div>
                            <div style="font-size:24px; font-weight:800;">{{ $classSummary['total'] }}</div>
                        </div>
                        <div>
                            <div class="muted" style="font-size:12px;">Sudah Check In</div>
                            <div style="font-size:24px; font-weight:800; color:var(--success);">{{ $classSummary['present'] }}</div>
                        </div>
                        <div>
                            <div class="muted" style="font-size:12px;">Belum Berangkat</div>
                            <div style="font-size:24px; font-weight:800; color:var(--warning);">{{ $classSummary['absent'] }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card" style="padding:18px; color:var(--muted);">
                    Belum ada booking kelas hari ini.
                </div>
            @endforelse
        </div>
    </section>

    <section class="card" style="overflow:hidden;">
        <div style="padding:16px 18px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; gap:12px;">
            <div style="font-weight:800;">Booking Hari Ini</div>
            <div class="muted" style="font-size:13px;">Manual attendance tersedia untuk koreksi cepat</div>
        </div>

        <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:860px;">
                <thead>
                    <tr style="background:#f9fafb; text-align:left;">
                        <th style="padding:12px 16px; border-bottom:1px solid var(--border);">Kelas</th>
                        <th style="padding:12px 16px; border-bottom:1px solid var(--border);">Jam</th>
                        <th style="padding:12px 16px; border-bottom:1px solid var(--border);">Member</th>
                        <th style="padding:12px 16px; border-bottom:1px solid var(--border);">Card</th>
                        <th style="padding:12px 16px; border-bottom:1px solid var(--border);">Phone</th>
                        <th style="padding:12px 16px; border-bottom:1px solid var(--border);">Attendance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $schedule = $booking->classSchedule;
                            $member = $booking->member;
                            $isPresent = (int) $booking->status === 1;
                        @endphp
                        <tr>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--border); font-weight:700;">
                                {{ $schedule?->name ?? '-' }}
                                <div class="muted" style="font-size:12px;">{{ $schedule?->branchStore?->name ?? '-' }}</div>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--border); white-space:nowrap;">
                                {{ $schedule?->time_start ? \Carbon\Carbon::parse($schedule->time_start)->format('H:i') : '--:--' }}
                                -
                                {{ $schedule?->time_end ? \Carbon\Carbon::parse($schedule->time_end)->format('H:i') : '--:--' }}
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--border);">
                                {{ $member?->full_name ?? $booking->name ?? '-' }}
                                <div class="muted" style="font-size:12px;">{{ $member?->email ?? $booking->email ?? '-' }}</div>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--border);">{{ $member?->card_number ?? '-' }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--border);">{{ $member?->phone_number ?? $booking->phone ?? '-' }}</td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--border);">
                                @if($isPresent)
                                    <button class="btn btn-danger btn-small" type="button" wire:click="markAbsent({{ $booking->id }})">
                                        Hadir
                                    </button>
                                @else
                                    <button class="btn btn-success btn-small" type="button" wire:click="markPresent({{ $booking->id }})">
                                        Belum
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:24px 16px; text-align:center;" class="muted">
                                Belum ada booking kelas hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    function initializeAdminClassQrScanner() {
        const dialog = document.getElementById('admin-class-qr-dialog');
        if (!dialog || dialog.dataset.scannerInitialized === 'true') return;
        dialog.dataset.scannerInitialized = 'true';

        const openButton = document.getElementById('open-admin-class-qr-scanner');
        const closeButton = document.getElementById('close-admin-class-qr-scanner');
        const startButton = document.getElementById('start-admin-class-qr-scanner');
        const stopButton = document.getElementById('stop-admin-class-qr-scanner');
        const imageInput = document.getElementById('admin-class-qr-image');
        const status = document.getElementById('admin-class-qr-status');
        const cardInput = document.getElementById('cardNumber');
        const form = document.getElementById('admin-class-check-in-form');
        const scanner = new Html5Qrcode('admin-class-qr-reader');
        let isRunning = false;
        let isSubmitting = false;

        function submitCardNumber(decodedText) {
            const cardNumber = decodedText.trim();
            if (!cardNumber || isSubmitting) return;

            isSubmitting = true;
            cardInput.value = cardNumber;
            cardInput.dispatchEvent(new Event('input', { bubbles: true }));
            status.textContent = 'QR ditemukan. Memproses kehadiran...';

            const submit = function() {
                dialog.close();
                form.requestSubmit();
            };

            if (isRunning) scanner.stop().then(submit).catch(submit);
            else submit();
        }

        async function stopScanner() {
            if (isRunning) await scanner.stop().catch(function() {});
            isRunning = false;
            startButton.hidden = false;
            startButton.disabled = false;
            stopButton.hidden = true;
        }

        openButton.addEventListener('click', function() {
            isSubmitting = false;
            dialog.showModal();
        });

        closeButton.addEventListener('click', async function() {
            await stopScanner();
            dialog.close();
        });

        startButton.addEventListener('click', async function() {
            startButton.disabled = true;
            status.textContent = 'Meminta izin kamera...';
            try {
                await scanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    submitCardNumber,
                    function() {}
                );
                isRunning = true;
                startButton.hidden = true;
                stopButton.hidden = false;
                status.textContent = 'Kamera aktif. Arahkan ke QR card member.';
            } catch (error) {
                startButton.disabled = false;
                status.textContent = 'Kamera tidak dapat dibuka. Periksa izin kamera atau pilih gambar QR.';
            }
        });

        stopButton.addEventListener('click', stopScanner);

        imageInput.addEventListener('change', async function(event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                await stopScanner();
                submitCardNumber(await scanner.scanFile(file, true));
            } catch (error) {
                status.textContent = 'QR tidak ditemukan pada gambar.';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initializeAdminClassQrScanner);
    document.addEventListener('livewire:navigated', initializeAdminClassQrScanner);
</script>
