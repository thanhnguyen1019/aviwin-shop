<?php

namespace App\Services\Customer\Address;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function getByUser(
        User $user
    ): Collection {
        return $user->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();
    }

    public function create(
        User $user,
        array $data
    ): Address {
        return DB::transaction(function () use ($user, $data) {

            $isFirstAddress = !$user->addresses()->exists();

            $isDefault = $isFirstAddress
                || (bool) ($data['is_default'] ?? false);

            if ($isDefault) {
                $user->addresses()
                    ->update([
                        'is_default' => false,
                    ]);
            }

            return $user->addresses()
                ->create([
                    ...$data,
                    'is_default' => $isDefault,
                ]);
        });
    }

    public function update(
        User $user,
        Address $address,
        array $data
    ): Address {
        $this->ensureBelongsToUser(
            $user,
            $address
        );

        return DB::transaction(function () use (
            $user,
            $address,
            $data
        ) {
            if (
                array_key_exists('is_default', $data)
                && (bool) $data['is_default']
            ) {
                $user->addresses()
                    ->where('id', '!=', $address->id)
                    ->update([
                        'is_default' => false,
                    ]);
            }
            if (
    array_key_exists('is_default', $data)
    && $data['is_default'] === false
    && $address->is_default
) {
    $hasOtherAddress = $user->addresses()
        ->where('id', '!=', $address->id)
        ->exists();

    if ($hasOtherAddress) {
        unset($data['is_default']);
    }
}
            $address->update($data);

            return $address->refresh();
        });
    }

    public function delete(
        User $user,
        Address $address
    ): void {
        $this->ensureBelongsToUser(
            $user,
            $address
        );

        DB::transaction(function () use ($user, $address) {

            $wasDefault = $address->is_default;

            $address->delete();

            if (!$wasDefault) {
                return;
            }

            $nextAddress = $user->addresses()
                ->latest('id')
                ->first();

            if ($nextAddress) {
                $nextAddress->update([
                    'is_default' => true,
                ]);
            }
        });
    }

    private function ensureBelongsToUser(
        User $user,
        Address $address
    ): void {
        abort_unless(
            $address->user_id === $user->id,
            404
        );
    }
}