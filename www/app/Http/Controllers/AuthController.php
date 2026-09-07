<?php

namespace App\Http\Controllers;

use App\Services\TelegramNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('pages.auth.login', ['title' => 'Masuk']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            app(TelegramNotifier::class)->send(
                sprintf(
                    "⚠️ <b>Login GAGAL</b> %s\n\n📧 Email: <code>%s</code>\n🖥️ IP: <code>%s</code>\n🕒 Waktu: %s",
                    config('app.name'),
                    e($credentials['email']),
                    e((string) $request->ip()),
                    now()->format('d-m-Y H:i:s'),
                )
            );

            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $user = Auth::user();

        app(TelegramNotifier::class)->send(
            sprintf(
                "🔓 <b>Login BERHASIL</b> %s\n\n👤 Nama: %s\n📧 Email: <code>%s</code>\n🖥️ IP: <code>%s</code>\n🕒 Waktu: %s",
                config('app.name'),
                e($user?->name ?? ''),
                e($user?->email ?? $credentials['email']),
                e((string) $request->ip()),
                now()->format('d-m-Y H:i:s'),
            )
        );

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
