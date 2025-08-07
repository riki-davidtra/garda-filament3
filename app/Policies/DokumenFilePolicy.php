<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\DokumenFile;
use App\Models\User;

class DokumenFilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any DokumenFile');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DokumenFile $dokumenfile): bool
    {
        return $user->checkPermissionTo('view DokumenFile');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create DokumenFile');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DokumenFile $dokumenfile): bool
    {
        return $user->checkPermissionTo('update DokumenFile');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DokumenFile $dokumenfile): bool
    {
        return $user->checkPermissionTo('delete DokumenFile');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any DokumenFile');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DokumenFile $dokumenfile): bool
    {
        return $user->checkPermissionTo('restore DokumenFile');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any DokumenFile');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, DokumenFile $dokumenfile): bool
    {
        return $user->checkPermissionTo('replicate DokumenFile');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder DokumenFile');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DokumenFile $dokumenfile): bool
    {
        return $user->checkPermissionTo('force-delete DokumenFile');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any DokumenFile');
    }
}
