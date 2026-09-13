<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Totp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (! session()->has('two_factor_pending_user')) {
            return redirect()->route('home');
        }

        return view('auth.two-factor-verify');
    }

    public function store(Request $request): RedirectResponse
    {
        $pendingUser = session('two_factor_pending_user');

        if (! $pendingUser) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required_without:recovery_code', 'nullable', 'string', 'max:32'],
            'recovery_code' => ['required_without:code', 'nullable', 'string', 'max:20'],
        ]);

        $user = User::findOrFail($pendingUser);
        $secret = $user->two_factor_secret ? decrypt($user->two_factor_secret) : null;
        $input = $request->string('code')->toString() ?: $request->string('recovery_code')->toString();

        $validTotp = $secret && Totp::verify($input, $secret);
        $validRecovery = $this->isValidRecoveryCode($user, $input);

        if (! $validTotp && ! $validRecovery) {
            return back()->withErrors(['code' => 'Kode atau kode pemulihan tidak valid.']);
        }

        session()->forget('two_factor_pending_user');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('orders.index'))->with('status', 'Autentikasi dua langkah berhasil.');
    }

    private function isValidRecoveryCode(User $user, string $input): bool
    {
        $codes = $user->two_factor_recovery_codes ? json_decode(decrypt($user->two_factor_recovery_codes), true) : [];

        foreach ($codes as $index => $hash) {
            if (password_verify($input, $hash)) {
                unset($codes[$index]);
                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($codes))),
                ])->save();

                return true;
            }
        }

        return false;
    }
}
