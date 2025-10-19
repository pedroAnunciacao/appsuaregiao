<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function sendResetEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email não encontrado.'], 404);
        }

        $token = Str::random(64);

        // Armazena o hash do token no banco
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => Carbon::now()]
        );

        $resetLink = url("/reset-password?token={$token}&email={$user->email}");

        Mail::send('emails.password_reset', ['resetLink' => $resetLink], function($message) use ($user) {
            $message->to($user->email)
                    ->subject('Redefinir Senha');
        });

        return response()->json(['message' => 'Email enviado com sucesso.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'token'=>'required|string',
            'password'=>'required|string|confirmed|min:8'
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        // Usa Hash::check para comparar token enviado com o hash armazenado
        if (!$record || !Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'Token inválido.'], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Senha resetada com sucesso.']);
    }
}