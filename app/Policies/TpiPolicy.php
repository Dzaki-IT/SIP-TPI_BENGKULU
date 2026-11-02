<?php

namespace App\Policies;

use App\Models\Tpi;
use App\Models\User;

class TpiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewOperationalData();
    }

    public function view(User $user, Tpi $tpi): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isPimpinan();
    }

    public function update(User $user, Tpi $tpi): bool
    {
        return $user->isPimpinan();
    }

    public function delete(User $user, Tpi $tpi): bool
    {
        return $user->isPimpinan();
    }
}
