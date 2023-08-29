<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function update(User $user, array $payload): User
    {
        $user->update($payload);

        return $user;
    }
}
