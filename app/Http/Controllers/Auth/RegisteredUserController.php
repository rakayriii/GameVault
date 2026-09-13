<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Support\Totp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse|View
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'max:40', 'regex:/^[a-zA-Z0-9._]+$/', Rule::unique('users')],
            'email' => ['required', 'string', 'email', 'max:190', Rule::unique('users')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $enableTwoFactor = $request->boolean('two_factor');

        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'username' => $request->string('username')->toString(),
            'email' => $request->string('email')->toString(),
            'phone' => $request->string('phone')->toString() ?: null,
            'password' => $request->string('password')->toString(),
            'role' => UserRole::Buyer,
            'two_factor_enabled' => $enableTwoFactor,
        ]);

        Wallet::query()->create(['user_id' => $user->id]);

        $setup = null;

        if ($enableTwoFactor) {
            $secret = Totp::generateSecret();
            $codes = collect(range(1, 8))->map(fn () => Str::upper(Str::random(6).'-'.Str::random(4)))->all();

            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode(array_map(fn ($c) => password_hash($c, PASSWORD_DEFAULT), $codes))),
                'two_factor_confirmed_at' => now(),
            ])->save();

            $setup = [
                'secret' => $secret,
                'qrcode' => Totp::provisioningUri($secret, $user->email),
                'codes' => $codes,
            ];
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($setup) {
            session()->flash('two_factor_setup', $setup);

            return view('auth.two-factor-setup', $setup);
        }

        return redirect()->intended(route('orders.index'))->with('status', 'Selamat datang di GameVault, '.$user->username.'!');
    }
}
