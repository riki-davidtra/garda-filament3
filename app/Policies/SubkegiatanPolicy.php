<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Subkegiatan;
use App\Models\User;

class SubkegiatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Subkegiatan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subkegiatan $subkegiatan): bool
    {
        return $user->checkPermissionTo('view Subkegiatan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Subkegiatan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subkegiatan $subkegiatan): bool
    {
        return $user->checkPermissionTo('update Subkegiatan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subkegiatan $subkegiatan): bool
    {
        return $user->checkPermissionTo('delete Subkegiatan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Subkegiatan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subkegiatan $subkegiatan): bool
    {
        return $user->checkPermissionTo('restore Subkegiatan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Subkegiatan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Subkegiatan $subkegiatan): bool
    {
        return $user->checkPermissionTo('replicate Subkegiatan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Subkegiatan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subkegiatan $subkegiatan): bool
    {
        return $user->checkPermissionTo('force-delete Subkegiatan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Subkegiatan');
    }
}
