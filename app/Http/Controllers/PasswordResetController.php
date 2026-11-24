<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showResetForm(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        return response()->json([
            'email' => $validated['email'],
            'token' => $token,
        ]);
    }

    public function sendResetLink(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink($request->validated());

        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'message' => 'Terlalu banyak permintaan. Silakan coba beberapa saat lagi.',
            ], 429);
        }

        if ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER) {
            return response()->json([
                'message' => 'Jika email terdaftar, tautan reset kata sandi akan dikirim.',
            ]);
        }

        return response()->json(['message' => trans($status)], 422);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->validated(),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json(['message' => trans($status)], 422);
        }

        return response()->json([
            'message' => 'Kata sandi berhasil diubah.',
        ]);
    }
}
