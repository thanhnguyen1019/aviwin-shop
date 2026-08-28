<?php

namespace App\Services\Customer\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileService
{
    public function update(
        User $user,
        array $data
    ): User {
        $user->update($data);

        return $user->refresh();
    }

    public function changePassword(
        User $user,
        string $currentPassword,
        string $newPassword
    ): void {
        if (
            !Hash::check(
                $currentPassword,
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'Mật khẩu hiện tại không chính xác.',
                ],
            ]);
        }

        $user->update([
            'password' => $newPassword,
        ]);
    }
}