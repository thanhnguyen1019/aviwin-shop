<?php

namespace App\Services\Customer\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'role' => User::ROLE_CUSTOMER,
            'is_active' => true,
        ]);

        $token = $user
            ->createToken('customer-register')
            ->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login(array $data): array
    {
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
        if (!$user->isActive()) {
            throw new AuthenticationException(
                'Tài khoản của bạn hiện đang bị khóa.'
            );
        }

        $deviceName = $data['device_name']
            ?? 'customer';

        $token = $user
            ->createToken($deviceName)
            ->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token) {
            $token->delete();
        }
    }
}