<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class RoleLoginController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas'])->onlyInput('email');
        }

        // Administrador: segunda etapa con código por correo
        if ($user->role === 'admin') {
            $code = (string) random_int(100000, 999999);

            $user->update([
                'two_factor_code' => Hash::make($code),
                'two_factor_expires_at' => now()->addMinutes(5),
            ]);

            Mail::to($user->email)->send(new TwoFactorCodeMail($code));

            $request->session()->put('2fa_user_id', $user->id);

            return redirect()->to(
                URL::temporarySignedRoute('two-factor.show', now()->addMinutes(5))
            );
        }

        // Empleado: acceso directo
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function showVerify(Request $request)
    {
        abort_unless($request->session()->has('2fa_user_id'), 403);

        return view('auth.two-factor');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $user = User::find($request->session()->get('2fa_user_id'));
        abort_unless($user, 403);

        $vencido = !$user->two_factor_expires_at || now()->greaterThan($user->two_factor_expires_at);

        if ($vencido || !Hash::check($request->code, $user->two_factor_code)) {
            return back()->withErrors(['code' => 'Código inválido o vencido']);
        }

        $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);
        $request->session()->forget('2fa_user_id');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}