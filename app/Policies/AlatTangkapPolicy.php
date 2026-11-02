<?php

namespace App\Policies;

use App\Models\AlatTangkap;
use App\Models\User;

class AlatTangkapPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewOperationalData();
    }

    public function view(User $user, AlatTangkap $alatTangkap): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->canManageAssignedTpiData();
    }

    public function update(User $user, AlatTangkap $alatTangkap): bool
    {
        return $alatTangkap->nelayan?->tpi_id !== null && $user->canManageTpiData($alatTangkap->nelayan->tpi_id);
    }

    public function delete(User $user, AlatTangkap $alatTangkap): bool
    {
        return $this->update($user, $alatTangkap);
    }
}
