<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
class MemberAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $request->session()->put('member_return_to', url()->previous());

        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Cek berdasarkan email dulu (paling penting)
        $member = Member::where('email', $googleUser->getEmail())->first();

        // Ambil return URL, default fallback ke homepage
        $returnTo = $request->session()->pull('member_return_to', '/');
        if (! $member) {
            $returnTo = $request->session()->pull('member_return_to', '/');

            $request->session()->flash('auth_error', 'Email ' . $googleUser->getEmail() . ' tidak terdaftar sebagai member. Silakan hubungi admin.');

            return redirect()->to($returnTo);
        } else {
            // kalau sudah ada, update google_id/avatar kalau kosong / berubah
            $member->update([
                'google_id' => $member->google_id ?? $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        $member->forceFill(['last_login_at' => now()])->save();

        // Login pakai guard member
        Auth::guard('member')->login($member, remember: true);

        return redirect()->to($returnTo);
    }

    public function logout(Request $request)
    {
        Auth::guard('member')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back();
    }
}