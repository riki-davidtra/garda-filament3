<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\FileTemplatDokumen;
use App\Models\User;

class FileTemplatDokumenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any FileTemplatDokumen');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FileTemplatDokumen $filetemplatdokumen): bool
    {
        return $user->checkPermissionTo('view FileTemplatDokumen');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create FileTemplatDokumen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FileTemplatDokumen $filetemplatdokumen): bool
    {
        return $user->checkPermissionTo('update FileTemplatDokumen');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FileTemplatDokumen $filetemplatdokumen): bool
    {
        return $user->checkPermissionTo('delete FileTemplatDokumen');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any FileTemplatDokumen');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FileTemplatDokumen $filetemplatdokumen): bool
    {
        return $user->checkPermissionTo('restore FileTemplatDokumen');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any FileTemplatDokumen');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, FileTemplatDokumen $filetemplatdokumen): bool
    {
        return $user->checkPermissionTo('replicate FileTemplatDokumen');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder FileTemplatDokumen');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FileTemplatDokumen $filetemplatdokumen): bool
    {
        return $user->checkPermissionTo('force-delete FileTemplatDokumen');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any FileTemplatDokumen');
    }
}
