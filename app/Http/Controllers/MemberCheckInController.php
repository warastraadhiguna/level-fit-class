<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MemberCheckInController extends Controller
{
    public function scanner(Request $request)
    {
        return view('member.check-in-scanner', [
            'pageTitle' => 'Scan QR Membership - Level FIT',
            'heading' => 'Membership Check In / Check Out',
            'description' => 'Arahkan kamera ke QR membership yang tampil di meja resepsionis.',
            'qrPrefix' => 'LEVELFIT_CHECKIN:',
            'formAction' => route('member.check-in.toggle'),
            'returnUrl' => $this->scannerReturnUrl($request, 'membership_check_in_return_to'),
        ]);
    }

    public function trainerSessionScanner(Request $request)
    {
        return view('member.check-in-scanner', [
            'pageTitle' => 'Scan QR Trainer Session - Level FIT',
            'heading' => 'Trainer Session Check In / Check Out',
            'description' => 'Arahkan kamera ke QR trainer session yang tampil di meja resepsionis.',
            'qrPrefix' => 'LEVELFIT_TRAINER_SESSION:',
            'formAction' => route('member.trainer-session-check-in.toggle'),
            'returnUrl' => $this->scannerReturnUrl($request, 'trainer_session_check_in_return_to'),
        ]);
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'qr_payload' => ['required', 'string', 'max:4096', 'starts_with:LEVELFIT_CHECKIN:'],
        ], [
            'qr_payload.starts_with' => 'QR code ini bukan QR check-in Level FIT.',
        ]);

        $baseUrl = rtrim((string) config('services.level_fit_admin.url'), '/');
        $secret = (string) config('services.level_fit_admin.qr_check_in_secret');

        if ($baseUrl === '' || $secret === '') {
            return back()->with('error', 'Integrasi QR check-in belum dikonfigurasi oleh admin.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->withHeaders(['X-QR-Check-In-Secret' => $secret])
                ->post($baseUrl . '/api/member-qr-check-in/toggle', [
                    'member_id' => (int) $request->user('member')->id,
                    'qr_token' => $validated['qr_payload'],
                ]);
        } catch (ConnectionException $exception) {
            return back()->with('error', 'Server check-in tidak dapat dihubungi. Silakan coba lagi atau hubungi resepsionis.');
        }

        $message = $response->json('message') ?: 'Proses check-in gagal.';
        if (!$response->successful()) {
            return back()->with('error', $message);
        }

        $status = $response->json('status');
        if ($status === 'duplicate') {
            return back()->with('info', $message);
        }

        return back()->with('success', $message)->with('check_in_status', $status);
    }

    public function toggleTrainerSession(Request $request)
    {
        $validated = $request->validate([
            'qr_payload' => ['required', 'string', 'max:4096', 'starts_with:LEVELFIT_TRAINER_SESSION:'],
        ], [
            'qr_payload.starts_with' => 'QR code ini bukan QR trainer session Level FIT.',
        ]);

        $baseUrl = rtrim((string) config('services.level_fit_admin.url'), '/');
        $secret = (string) config('services.level_fit_admin.qr_check_in_secret');

        if ($baseUrl === '' || $secret === '') {
            return back()->with('error', 'Integrasi QR check-in belum dikonfigurasi oleh admin.');
        }

        try {
            $response = Http::acceptJson()
                ->timeout(10)
                ->withHeaders(['X-QR-Check-In-Secret' => $secret])
                ->post($baseUrl . '/api/trainer-session-qr-check-in/toggle', [
                    'member_id' => (int) $request->user('member')->id,
                    'qr_token' => $validated['qr_payload'],
                ]);
        } catch (ConnectionException $exception) {
            return back()->with('error', 'Server check-in tidak dapat dihubungi. Silakan coba lagi atau hubungi resepsionis.');
        }

        $message = $response->json('message') ?: 'Proses trainer session check-in gagal.';
        if (!$response->successful()) {
            return back()->with('error', $message);
        }

        $status = $response->json('status');
        if ($status === 'duplicate') {
            return back()->with('info', $message);
        }

        return back()
            ->with('success', $message)
            ->with('check_in_status', $status)
            ->with('trainer_session', $response->json('trainer_session'));
    }

    private function scannerReturnUrl(Request $request, string $sessionKey): string
    {
        $requestedReturnUrl = (string) $request->query('return_to', '');

        // Hanya menerima path lokal agar parameter ini tidak dapat menjadi open redirect.
        if ($requestedReturnUrl !== ''
            && str_starts_with($requestedReturnUrl, '/')
            && !str_starts_with($requestedReturnUrl, '//')) {
            $request->session()->put(
                $sessionKey,
                $request->getSchemeAndHttpHost() . $requestedReturnUrl
            );
        } else {
            $previousUrl = url()->previous();
            $previousPath = (string) parse_url($previousUrl, PHP_URL_PATH);

            if (str_starts_with($previousUrl, url('/'))
                && trim($previousPath, '/') !== trim($request->path(), '/')) {
                $request->session()->put($sessionKey, $previousUrl);
            }
        }

        return (string) $request->session()->get($sessionKey, url('/'));
    }
}
