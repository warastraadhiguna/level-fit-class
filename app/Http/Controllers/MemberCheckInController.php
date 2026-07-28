<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberCheckInController extends Controller
{
    public function cardQr(Request $request)
    {
        return view('member.card-qr', [
            'returnUrl' => $this->returnUrl($request),
            'member' => $request->user('member'),
        ]);
    }

    private function returnUrl(Request $request): string
    {
        $sessionKey = 'member_card_qr_return_to';
        $requestedReturnUrl = (string) $request->query('return_to', '');

        if ($requestedReturnUrl !== ''
            && str_starts_with($requestedReturnUrl, '/')
            && !str_starts_with($requestedReturnUrl, '//')) {
            $request->session()->put(
                $sessionKey,
                $request->getSchemeAndHttpHost() . $requestedReturnUrl
            );
        }

        return (string) $request->session()->get($sessionKey, url('/'));
    }
}
