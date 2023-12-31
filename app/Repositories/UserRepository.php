<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    /**
     * @param array<string, string|int> $payload
     */
    public function create(array $payload): User
    {
        return User::firstOrCreate(['email' => $payload['email']], $payload);
    }

    /**
     * @param array<string, string|int> $payload
     */
    public function update(User $user, array $payload): User
    {
        $user->update($payload);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
