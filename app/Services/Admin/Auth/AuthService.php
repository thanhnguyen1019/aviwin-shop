<?php

namespace App\Services\Admin\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(
        array $data
    ): array {
        $user = User::query()
            ->where(
                'email',
                strtolower($data['email'])
            )
            ->first();

        if (
            !$user
            || !Hash::check(
                $data['password'],
                $user->password
            )
        ) {
            throw new AuthenticationException(
                'Email hoặc mật khẩu không chính xác.'
            );
        }

        if (!$user->isAdmin()) {
            throw new AuthenticationException(
                'Tài khoản này không có quyền quản trị.'
            );
        }

        $deviceName =
            $data['device_name']
            ?? 'admin';

        $token = $user
            ->createToken($deviceName)
            ->plainTextToken;

        return [
            'admin' => $user,
            'token' => $token,
        ];
    }

    public function logout(
        User $user
    ): void {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }
}