<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Bagian;
use App\Models\User;

class BagianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Bagian');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bagian $bagian): bool
    {
        return $user->checkPermissionTo('view Bagian');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Bagian');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bagian $bagian): bool
    {
        return $user->checkPermissionTo('update Bagian');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bagian $bagian): bool
    {
        return $user->checkPermissionTo('delete Bagian');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Bagian');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bagian $bagian): bool
    {
        return $user->checkPermissionTo('restore Bagian');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Bagian');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Bagian $bagian): bool
    {
        return $user->checkPermissionTo('replicate Bagian');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Bagian');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bagian $bagian): bool
    {
        return $user->checkPermissionTo('force-delete Bagian');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Bagian');
    }
}
