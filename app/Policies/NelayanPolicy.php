<?php

namespace App\Policies;

use App\Models\Nelayan;
use App\Models\User;

class NelayanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewOperationalData();
    }

    public function view(User $user, Nelayan $nelayan): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->canManageAssignedTpiData();
    }

    public function update(User $user, Nelayan $nelayan): bool
    {
        return $nelayan->tpi_id !== null && $user->canManageTpiData($nelayan->tpi_id);
    }

    public function delete(User $user, Nelayan $nelayan): bool
    {
        return $this->update($user, $nelayan);
    }
}
