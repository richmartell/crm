<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('components.layout')]
class TwoFactorSettings extends Component
{
    public string $secret = '';
    public string $qrCodeUrl = '';
    public string $code = '';
    public bool $enabled = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->enabled = (bool) $user->two_factor_enabled;
        $sessionSecret = Session::get('two_factor_secret');

        if (! $sessionSecret) {
            $this->secret = $this->enabled && $user->two_factor_secret
                ? Crypt::decryptString($user->two_factor_secret)
                : (new Google2FA())->generateSecretKey();

            Session::put('two_factor_secret', $this->secret);
        } else {
            $this->secret = $sessionSecret;
        }

        $this->qrCodeUrl = (new Google2FA())
            ->getQRCodeUrl(config('app.name'), $user->email, $this->secret);
    }

    public function enable(): void
    {
        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $google2fa = new Google2FA();

        if (! $google2fa->verifyKey($this->secret, $this->code)) {
            $this->addError('code', 'Invalid verification code.');
            return;
        }

        Auth::user()->forceFill([
            'two_factor_secret' => Crypt::encryptString($this->secret),
            'two_factor_enabled' => true,
        ])->save();

        Session::forget('two_factor_secret');

        $this->reset(['code']);
        $this->enabled = true;

        session()->flash('status', 'Two-factor authentication enabled.');
    }

    public function disable(): void
    {
        Auth::user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
        ])->save();

        Session::forget('two_factor_secret');

        $this->reset(['code']);
        $this->enabled = false;
        $this->secret = (new Google2FA())->generateSecretKey();
        Session::put('two_factor_secret', $this->secret);
        $this->qrCodeUrl = (new Google2FA())
            ->getQRCodeUrl(config('app.name'), Auth::user()->email, $this->secret);

        session()->flash('status', 'Two-factor authentication disabled.');
    }

    public function render()
    {
        return view('livewire.two-factor-settings');
    }
}
