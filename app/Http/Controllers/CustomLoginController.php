<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use App\Support\PasswordHash;
use App\Http\Controllers\UserConfigController;
use Inertia\Inertia;

class CustomLoginController extends Controller
{
    /**
     * Zeige das Login-Formular
     */
    public function showLoginForm(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => $request->redirect]);
        }
        return Inertia::render('Auth/Login');
    }
    public function showResetForm(Request $request, $token)
{
    return Inertia::render('Auth/ResetPassword', [
    'token' => $token,
    'email' => $request->email,
]);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) use ($request) {
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
}
    /**
     * Silent Login für AJAX / API
     */
    public function loginSilent(Request $request)
    {
//         \Log::info('LOGIN-SILENT HIT');

        if(empty($request->password)) {
            return response()->json([
                'type'=>'info',
                'message' => 'Kein Login möglich',
                'user_id' => 7,
                "full_name"=>"Gast",
                "profile_photo_url"=>"008.jpg"
            ]);
        }

        if (!$this->login($request,true)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if(Auth::id() && Auth::id() != 7) {
            return response()->json([
                'type'=>'success',
                'message' => 'Sie wurden erfolgreich eingeloggt',
                'user_id' => Auth::id(),
                "full_name"=>Auth::user()->first_name ?? "Gast",
                "profile_photo_url"=>Auth::user()->profile_photo_path ?? "008.jpg",
            ]);
        }
    }

    /**
     * Login Logik (normal und silent)
     */
    public function login(Request $request, $silent=false)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = $request->input('email');
        $plainPassword = $request->input('password');
        $remember = $request->boolean('remember');

        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $user = \App\Models\User::where($field, $loginInput)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Benutzer nicht gefunden.']);
        }

        // Prüfe Passwort (neu + altes Backup)
        if (!Hash::check($plainPassword, $user->password)) {
            if ($user->password_old) {
                $hasher = new PasswordHash(8, true);
                if ($hasher->CheckPassword($plainPassword, $user->password_old)) {
                    $user->password = Hash::make($plainPassword);
                    $user->password_old = null;
                    $user->save();
                } else {
                    return back()->withErrors(['password' => 'Falsches Passwort.']);
                }
            } else {
                return back()->withErrors(['password' => 'Falsches Passwort.']);
            }
        }

        // Wenn 2FA aktiv
        if ($user->two_factor_secret && $user->two_factor_enabled && !$silent) {
            session([
                'two_factor:user:id' => $user->id,
                'two_factor:remember' => $remember,
            ]);
            return redirect()->route('two-factor.login');
        }

        // Kein 2FA → direkt einloggen
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // UserConfig aktualisieren
        UserConfigController::updateConfig(Auth::id());

        $redirect = $request->input('redirect') ?? session('url.intended');
        if ($redirect && !$silent) {
            session()->forget('url.intended');
            return Inertia::location($redirect . '?re=1');
        }

        if($silent) {
            return true;
        }

        return app(LoginResponse::class);
    }

    /**
     * 2FA aktivieren
     */
    public function enableTwoFactor(Request $request)
    {
        $user = $request->user();
        $user->two_factor_enabled = 1;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * 2FA deaktivieren
     */
    public function disableTwoFactor(Request $request)
    {
        $user = $request->user();
        $user->two_factor_enabled = 0;
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Zeige 2FA Challenge Formular
     */
    public function showTwoFactorForm()
    {
        if (!session()->has('two_factor:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Bitte zuerst E-Mail und Passwort eingeben.'
            ]);
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * 2FA Login prüfen
     */
    public function twoFactorLogin(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        if (!session()->has('two_factor:user:id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Bitte zuerst E-Mail und Passwort eingeben.'
            ]);
        }

        $user = \App\Models\User::find(session('two_factor:user:id'));
        $remember = session('two_factor:remember', false);

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Benutzer nicht gefunden.']);
        }

        if ($request->filled('code') && $user->verifyTwoFactorCode($request->code)) {
            // OTP korrekt
        } elseif ($request->filled('recovery_code') && $user->verifyRecoveryCode($request->recovery_code)) {
            // Recovery Code korrekt
        } else {
            return back()->withErrors(['code' => 'Ungültiger Zwei-Faktor-Code.']);
        }

        Auth::login($user, $remember);
        session()->forget(['two_factor:user:id', 'two_factor:remember']);

        return app(TwoFactorLoginResponse::class);
    }


public function sendRecoveryEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
}
    /**
     * Logout
     */
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Wichtig: url.intended entfernen, sonst redirect nach Dashboard
    // session()->forget('url.intended');
    // dd(Auth::user());
    // SPA-kompatibel redirect
     return Inertia::location(url('/'));
}
    /**
     * Passwort Recovery / ändern
     */
    public function pw_recovery(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
        'token' => 'required',
    ]);

    $user = User::where('email', $request->email)->firstOrFail();

    // Passwort zurücksetzen
    $user->password = Hash::make($request->password);
    $user->save();

    return redirect()->route('login')->with('status', 'Kennwort erfolgreich zurückgesetzt!');
}
    public function pw_recovery_old(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
            'password_confirmation' => 'required|string|confirmed|min:8',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Benutzer nicht gefunden.']);
        }

        $currentPassword = $request->input('current_password');

        if (!Hash::check($currentPassword, $user->password)) {
            if ($user->password_old) {
                $hasher = new PasswordHash(8, true);
                if (!$hasher->CheckPassword($currentPassword, $user->password_old)) {
                    return back()->withErrors(['current_password' => 'Falsches Passwort.']);
                }
            } else {
                return back()->withErrors(['current_password' => 'Falsches Passwort.']);
            }
        }

        // Neues Passwort speichern
        $user->fill([
            'password' => $request->input('new_password'),
            'password_old' => null,
        ])->save();

        return back()->with('success', 'Passwort erfolgreich geändert.');
    }
}
