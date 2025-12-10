<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Kelas;

/**
 * User Name Sync Observer
 * 
 * PURPOSE: Auto-update user name based on role and assignment
 * 
 * RULES:
 * - Kaprodi → "Kaprodi [Nama Jurusan]"
 * - Wali Kelas → "Wali Kelas [Nama Kelas]"
 * - Other roles → Keep original name (unless manually changed)
 * 
 * TRIGGERS:
 * - When user is assigned as Kaprodi (Jurusan.kaprodi_user_id updated)
 * - When user is assigned as Wali Kelas (Kelas.wali_kelas_user_id updated)
 * - When user role changes
 */
class UserNameSyncObserver
{
    /**
     * Handle the User "updated" event.
     * 
     * CRITICAL: When role changes, auto-detach invalid assignments
     */
    public function updated(User $user): void
    {
        // CRITICAL: If role changed, detach invalid assignments
        if ($user->wasChanged('role_id')) {
            $this->detachInvalidAssignments($user);
            $this->syncUserName($user);
        }
    }
    
    /**
     * Detach user from assignments that are invalid for their new role.
     * 
     * EXAMPLES:
     * - Kaprodi → Guru: Remove from all Jurusan
     * - Wali Kelas → Guru: Remove from all Kelas
     * - Wali Murid → Guru: Remove from all Siswa
     * 
     * @param User $user
     * @return void
     */
    private function detachInvalidAssignments(User $user): void
    {
        $newRole = $user->role;
        
        if (!$newRole) {
            return;
        }
        
        // Get old role from original attributes
        $oldRoleId = $user->getOriginal('role_id');
        $oldRole = $oldRoleId ? \App\Models\Role::find($oldRoleId) : null;
        
        if (!$oldRole) {
            return;
        }
        
        // CASE 1: Was Kaprodi, now something else → Detach from Jurusan
        if ($oldRole->nama_role === 'Kaprodi' && $newRole->nama_role !== 'Kaprodi') {
            Jurusan::where('kaprodi_user_id', $user->id)
                ->update(['kaprodi_user_id' => null]);
            
            \Log::info("User {$user->username} role changed from Kaprodi to {$newRole->nama_role}. Detached from all Jurusan.");
        }
        
        // CASE 2: Was Wali Kelas, now something else → Detach from Kelas
        if ($oldRole->nama_role === 'Wali Kelas' && $newRole->nama_role !== 'Wali Kelas') {
            Kelas::where('wali_kelas_user_id', $user->id)
                ->update(['wali_kelas_user_id' => null]);
            
            \Log::info("User {$user->username} role changed from Wali Kelas to {$newRole->nama_role}. Detached from all Kelas.");
        }
        
        // CASE 3: Was Wali Murid, now something else → Detach from Siswa
        if ($oldRole->nama_role === 'Wali Murid' && $newRole->nama_role !== 'Wali Murid') {
            \App\Models\Siswa::where('wali_murid_user_id', $user->id)
                ->update(['wali_murid_user_id' => null]);
            
            \Log::info("User {$user->username} role changed from Wali Murid to {$newRole->nama_role}. Detached from all Siswa.");
        }
    }
    
    /**
     * Sync user name based on role and assignment.
     * 
     * EXCLUSION: Developer role is NEVER auto-synced
     * Developer can be assigned to multiple things for testing,
     * so their name should stay as-is.
     * 
     * @param User $user
     * @return void
     */
    public function syncUserName(User $user): void
    {
        $role = $user->role;
        
        if (!$role) {
            return;
        }
        
        // CRITICAL: Skip auto-sync for Developer role
        if ($role->nama_role === 'Developer') {
            return; // Developer names stay as-is, even if assigned to Jurusan/Kelas
        }
        
        $newName = null;
        
        switch ($role->nama_role) {
            case 'Kaprodi':
                // Find jurusan where this user is kaprodi
                $jurusan = Jurusan::where('kaprodi_user_id', $user->id)->first();
                if ($jurusan) {
                    $newName = "Kaprodi {$jurusan->nama_jurusan}";
                }
                break;
                
            case 'Wali Kelas':
                // Find kelas where this user is wali kelas
                $kelas = Kelas::where('wali_kelas_user_id', $user->id)->first();
                if ($kelas) {
                    $newName = "Wali Kelas {$kelas->nama_kelas}";
                }
                break;
                
            default:
                // For other roles, keep original or use role name
                // Only update if name follows pattern (starts with role name)
                if (str_starts_with($user->nama, 'Kaprodi ') || str_starts_with($user->nama, 'Wali Kelas ')) {
                    $newName = $role->nama_role;
                }
                break;
        }
        
        // Update name if we have new name and it's different
        if ($newName && $newName !== $user->nama) {
            // Use updateQuietly to prevent infinite loop
            $user->updateQuietly(['nama' => $newName]);
        }
    }
}
