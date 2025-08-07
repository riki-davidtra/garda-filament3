<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\JenisDokumen;
use App\Models\User;

class JenisDokumenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any JenisDokumen');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JenisDokumen $jenisdokumen): bool
    {
        return $user->checkPermissionTo('view JenisDokumen');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create JenisDokumen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JenisDokumen $jenisdokumen): bool
    {
        return $user->checkPermissionTo('update JenisDokumen');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JenisDokumen $jenisdokumen): bool
    {
        return $user->checkPermissionTo('delete JenisDokumen');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any JenisDokumen');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JenisDokumen $jenisdokumen): bool
    {
        return $user->checkPermissionTo('restore JenisDokumen');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any JenisDokumen');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, JenisDokumen $jenisdokumen): bool
    {
        return $user->checkPermissionTo('replicate JenisDokumen');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder JenisDokumen');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JenisDokumen $jenisdokumen): bool
    {
        return $user->checkPermissionTo('force-delete JenisDokumen');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any JenisDokumen');
    }
}
