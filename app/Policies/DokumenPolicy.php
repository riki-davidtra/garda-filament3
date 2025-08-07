<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Dokumen;
use App\Models\User;

class DokumenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Dokumen');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dokumen $dokumen): bool
    {
        return $user->checkPermissionTo('view Dokumen');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Dokumen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dokumen $dokumen): bool
    {
        return $user->checkPermissionTo('update Dokumen');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dokumen $dokumen): bool
    {
        return $user->checkPermissionTo('delete Dokumen');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Dokumen');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Dokumen $dokumen): bool
    {
        return $user->checkPermissionTo('restore Dokumen');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Dokumen');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Dokumen $dokumen): bool
    {
        return $user->checkPermissionTo('replicate Dokumen');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Dokumen');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Dokumen $dokumen): bool
    {
        return $user->checkPermissionTo('force-delete Dokumen');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Dokumen');
    }
}
