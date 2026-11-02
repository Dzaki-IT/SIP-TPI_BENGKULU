<?php

namespace App\Policies;

use App\Models\Kapal;
use App\Models\User;

class KapalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewOperationalData();
    }

    public function view(User $user, Kapal $kapal): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->canManageAssignedTpiData();
    }

    public function update(User $user, Kapal $kapal): bool
    {
        return $kapal->nelayan?->tpi_id !== null && $user->canManageTpiData($kapal->nelayan->tpi_id);
    }

    public function delete(User $user, Kapal $kapal): bool
    {
        return $this->update($user, $kapal);
    }
}
