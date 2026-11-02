<?php

namespace App\Policies;

use App\Models\JenisIkan;
use App\Models\User;

class JenisIkanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewOperationalData();
    }

    public function view(User $user, JenisIkan $jenisIkan): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isPimpinan();
    }

    public function update(User $user, JenisIkan $jenisIkan): bool
    {
        return $user->isPimpinan();
    }

    public function delete(User $user, JenisIkan $jenisIkan): bool
    {
        return $user->isPimpinan();
    }
}
