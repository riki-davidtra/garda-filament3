<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Pengaduan;
use App\Models\User;

class PengaduanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Pengaduan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pengaduan $pengaduan): bool
    {
        return $user->checkPermissionTo('view Pengaduan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Pengaduan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pengaduan $pengaduan): bool
    {
        return $user->checkPermissionTo('update Pengaduan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pengaduan $pengaduan): bool
    {
        return $user->checkPermissionTo('delete Pengaduan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Pengaduan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pengaduan $pengaduan): bool
    {
        return $user->checkPermissionTo('restore Pengaduan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Pengaduan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Pengaduan $pengaduan): bool
    {
        return $user->checkPermissionTo('replicate Pengaduan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Pengaduan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pengaduan $pengaduan): bool
    {
        return $user->checkPermissionTo('force-delete Pengaduan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Pengaduan');
    }
}
