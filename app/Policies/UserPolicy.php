<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPimpinan();
    }

    public function view(User $user, User $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isPimpinan();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isPimpinan();
    }
}
