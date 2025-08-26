<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\FormatFile;
use App\Models\User;

class FormatFilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any FormatFile');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FormatFile $formatfile): bool
    {
        return $user->checkPermissionTo('view FormatFile');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create FormatFile');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FormatFile $formatfile): bool
    {
        return $user->checkPermissionTo('update FormatFile');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FormatFile $formatfile): bool
    {
        return $user->checkPermissionTo('delete FormatFile');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any FormatFile');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FormatFile $formatfile): bool
    {
        return $user->checkPermissionTo('restore FormatFile');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any FormatFile');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, FormatFile $formatfile): bool
    {
        return $user->checkPermissionTo('replicate FormatFile');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder FormatFile');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FormatFile $formatfile): bool
    {
        return $user->checkPermissionTo('force-delete FormatFile');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any FormatFile');
    }
}
