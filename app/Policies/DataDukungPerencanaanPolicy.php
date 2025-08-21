<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\DataDukungPerencanaan;
use App\Models\User;

class DataDukungPerencanaanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DataDukungPerencanaan $datadukungperencanaan): bool
    {
        return $user->checkPermissionTo('view DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataDukungPerencanaan $datadukungperencanaan): bool
    {
        return $user->checkPermissionTo('update DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataDukungPerencanaan $datadukungperencanaan): bool
    {
        return $user->checkPermissionTo('delete DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DataDukungPerencanaan $datadukungperencanaan): bool
    {
        return $user->checkPermissionTo('restore DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, DataDukungPerencanaan $datadukungperencanaan): bool
    {
        return $user->checkPermissionTo('replicate DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DataDukungPerencanaan $datadukungperencanaan): bool
    {
        return $user->checkPermissionTo('force-delete DataDukungPerencanaan');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any DataDukungPerencanaan');
    }
}
