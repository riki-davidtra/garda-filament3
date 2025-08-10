<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Panduan;
use App\Models\User;

class PanduanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Panduan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Panduan $panduan): bool
    {
        return $user->checkPermissionTo('view Panduan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Panduan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Panduan $panduan): bool
    {
        return $user->checkPermissionTo('update Panduan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Panduan $panduan): bool
    {
        return $user->checkPermissionTo('delete Panduan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Panduan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Panduan $panduan): bool
    {
        return $user->checkPermissionTo('restore Panduan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Panduan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Panduan $panduan): bool
    {
        return $user->checkPermissionTo('replicate Panduan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Panduan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Panduan $panduan): bool
    {
        return $user->checkPermissionTo('force-delete Panduan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Panduan');
    }
}
