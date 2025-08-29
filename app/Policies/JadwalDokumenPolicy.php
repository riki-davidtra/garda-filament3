<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\JadwalDokumen;
use App\Models\User;

class JadwalDokumenPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any JadwalDokumen');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JadwalDokumen $jadwaldokumen): bool
    {
        return $user->checkPermissionTo('view JadwalDokumen');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create JadwalDokumen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JadwalDokumen $jadwaldokumen): bool
    {
        return $user->checkPermissionTo('update JadwalDokumen');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JadwalDokumen $jadwaldokumen): bool
    {
        return $user->checkPermissionTo('delete JadwalDokumen');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any JadwalDokumen');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JadwalDokumen $jadwaldokumen): bool
    {
        return $user->checkPermissionTo('restore JadwalDokumen');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any JadwalDokumen');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, JadwalDokumen $jadwaldokumen): bool
    {
        return $user->checkPermissionTo('replicate JadwalDokumen');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder JadwalDokumen');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JadwalDokumen $jadwaldokumen): bool
    {
        return $user->checkPermissionTo('force-delete JadwalDokumen');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any JadwalDokumen');
    }
}
