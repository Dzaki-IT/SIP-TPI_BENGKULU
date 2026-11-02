<?php

namespace App\Policies;

use App\Models\Pendaratan;
use App\Models\User;

class PendaratanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canViewOperationalData();
    }

    public function view(User $user, Pendaratan $pendaratan): bool
    {
        if (! $this->viewAny($user)) {
            return false;
        }

        return ! $user->isAdminTpi() || $user->tpi_id === $pendaratan->tpi_id;
    }

    public function create(User $user): bool
    {
        return $user->canManageAssignedTpiData();
    }

    public function update(User $user, Pendaratan $pendaratan): bool
    {
        return $user->canManageTpiData($pendaratan->tpi_id);
    }

    public function delete(User $user, Pendaratan $pendaratan): bool
    {
        return $this->update($user, $pendaratan);
    }

    public function submit(User $user, Pendaratan $pendaratan): bool
    {
        return $this->update($user, $pendaratan);
    }

    public function viewVerification(User $user): bool
    {
        return $user->canViewVerification();
    }

    public function verify(User $user, Pendaratan $pendaratan): bool
    {
        return $user->canVerifyPendaratan() && $pendaratan->status_verifikasi === 'diajukan';
    }
}
