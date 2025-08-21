<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Indikator;
use App\Models\User;

class IndikatorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Indikator');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Indikator $indikator): bool
    {
        return $user->checkPermissionTo('view Indikator');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Indikator');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Indikator $indikator): bool
    {
        return $user->checkPermissionTo('update Indikator');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Indikator $indikator): bool
    {
        return $user->checkPermissionTo('delete Indikator');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Indikator');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Indikator $indikator): bool
    {
        return $user->checkPermissionTo('restore Indikator');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Indikator');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Indikator $indikator): bool
    {
        return $user->checkPermissionTo('replicate Indikator');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Indikator');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Indikator $indikator): bool
    {
        return $user->checkPermissionTo('force-delete Indikator');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Indikator');
    }
}
