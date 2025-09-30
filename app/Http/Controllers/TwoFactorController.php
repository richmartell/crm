<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function create(): View
    {
        $user = Auth::user();

        if ($user->two_factor_enabled && ! Session::has('two_factor_secret')) {
            Session::put('two_factor_secret', Crypt::decryptString($user->two_factor_secret));
        }

        $secret = Session::get('two_factor_secret', (new Google2FA())->generateSecretKey());
        Session::put('two_factor_secret', $secret);

        $google2faUrl = (new Google2FA())
            ->getQRCodeUrl(config('app.name'), $user->email, $secret);

        return view('two-factor.settings', [
            'user' => $user,
            'secret' => $secret,
            'qrCodeUrl' => $google2faUrl,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $google2fa = new Google2FA();
        $secret = Session::get('two_factor_secret');
        $user = Auth::user();

        if (! $google2fa->verifyKey($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Invalid verification code.'])->withInput();
        }

        $user->forceFill([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_enabled' => true,
        ])->save();

        Session::forget('two_factor_secret');

        return redirect()->route('two-factor.settings')->with('status', 'Two-factor authentication enabled.');
    }

    public function destroy(): RedirectResponse
    {
        $user = Auth::user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ])->save();

        Session::forget('two_factor_secret');

        return redirect()->route('two-factor.settings')->with('status', 'Two-factor authentication disabled.');
    }

    public function showVerify(): View
    {
        abort_unless(Session::get('needs_two_factor'), 403);

        return view('two-factor.verify');
    }

    public function verify(Request $request): RedirectResponse
    {
        abort_unless(Session::get('needs_two_factor'), 403);

        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $google2fa = new Google2FA();
        $secret = Crypt::decryptString(Auth::user()->two_factor_secret);

        if (! $google2fa->verifyKey($secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'Invalid verification code.'])->withInput();
        }

        Session::forget('needs_two_factor');

        return redirect()->intended(route('contacts.index'));
    }
}
