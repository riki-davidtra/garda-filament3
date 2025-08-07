<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Subbagian;
use App\Models\User;

class SubbagianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Subbagian');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subbagian $subbagian): bool
    {
        return $user->checkPermissionTo('view Subbagian');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Subbagian');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subbagian $subbagian): bool
    {
        return $user->checkPermissionTo('update Subbagian');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subbagian $subbagian): bool
    {
        return $user->checkPermissionTo('delete Subbagian');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Subbagian');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subbagian $subbagian): bool
    {
        return $user->checkPermissionTo('restore Subbagian');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Subbagian');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Subbagian $subbagian): bool
    {
        return $user->checkPermissionTo('replicate Subbagian');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Subbagian');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subbagian $subbagian): bool
    {
        return $user->checkPermissionTo('force-delete Subbagian');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Subbagian');
    }
}
