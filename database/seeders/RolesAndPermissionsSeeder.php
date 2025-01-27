<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Delete existing permissions and roles
        Permission::query()->delete();
        Role::query()->delete();
        User::query()->delete();

        // Create Permissions
        $permissions = [
            // System Administration permissions
            'view.users',
            'create.users',
            'edit.users',
            'delete.users',
            'view.users.datatables',
            
            'view.roles',
            'create.roles',
            'edit.roles',
            'delete.roles',
            'view.roles.datatables',
            
            'view.permissions',
            'create.permissions',
            'edit.permissions',
            'delete.permissions',
            'view.permissions.datatables',
            
            // Dashboard permissions
            'access.dashboard',
            
            // Applicant management permissions
            'view.applicants',
            'create.applicants',
            'edit.applicants',
            'delete.applicants',
            'review.applicants',
            'view.applicants.datatables',
            
            // Board management permissions
            'view.boards',
            'view.application.boards',
            'manage.boards',
            
            // Export Declaration permissions
            'view.export.declarations',
            'create.export.declarations',
            'edit.export.declarations',
            'delete.export.declarations',
            'view.export.declarations.datatables',
            'access.species.data',
            
            // Settings permissions
            'manage.settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        
        // System Admin Role
        $sysAdminRole = Role::create(['name' => 'sys.admin']);
        $sysAdminRole->givePermissionTo([
            'view.users',
            'create.users',
            'edit.users',
            'delete.users',
            'view.users.datatables',
            'view.roles',
            'create.roles',
            'edit.roles',
            'delete.roles',
            'view.roles.datatables',
            'view.permissions',
            'create.permissions',
            'edit.permissions',
            'delete.permissions',
            'view.permissions.datatables',
            'manage.settings',
        ]);

        // License Admin Role
        $licAdminRole = Role::create(['name' => 'lic.admin']);
        $licAdminRole->givePermissionTo([
            'access.dashboard',
            'view.applicants',
            'create.applicants',
            'edit.applicants',
            'delete.applicants',
            'review.applicants',
            'view.applicants.datatables',
            'view.boards',
            'view.application.boards',
            'manage.boards',
            'view.export.declarations',
            'create.export.declarations',
            'edit.export.declarations',
            'delete.export.declarations',
            'view.export.declarations.datatables',
            'access.species.data',
            'manage.settings',
        ]);

        // License User Role
        $licUserRole = Role::create(['name' => 'lic.user']);
        $licUserRole->givePermissionTo([
            'access.dashboard',
           
            'create.applicants',
            'edit.applicants',
            'view.applicants.datatables',
            'view.export.declarations',
            'create.export.declarations',
            'edit.export.declarations',
            'view.export.declarations.datatables',
            'access.species.data',
        ]);

        // License Viewer Role
        $licViewerRole = Role::create(['name' => 'lic.viewer']);
        $licViewerRole->givePermissionTo([
            'access.dashboard',
            'view.applicants',
            'view.applicants.datatables',
            'view.export.declarations',
            'view.export.declarations.datatables',
        ]);

        // License View All Role
        $licViewAllRole = Role::create(['name' => 'lic.view.all']);
        $licViewAllRole->givePermissionTo([
            'access.dashboard',
            'view.applicants',
            'view.applicants.datatables',
            'view.export.declarations',
            'view.export.declarations.datatables',
            'view.boards',
            'view.application.boards',
        ]);

        // Create Users and Assign Roles
        
        // System Admin User
        $sysAdminUser = User::create([
            'name' => 'System Administrator',
            'email' => 'sysadmin@mfmrd.gov.ki',
            'password' => bcrypt('password')
        ]);
        $sysAdminUser->assignRole($sysAdminRole);

        // License Admin User
        $adminUser = User::create([
            'name' => 'Tooreka Temari',
            'email' => 'toorekat@mfmrd.gov.ki',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole($licAdminRole);

        // License User
        $licenseUser = User::create([
            'name' => 'Joseph Teuea Toatu',
            'email' => 'josepht@mfmrd.gov.ki',
            'password' => bcrypt('password')
        ]);
        $licenseUser->assignRole($licUserRole);

        // License Viewer
        $licenseViewer = User::create([
            'name' => 'License Viewer',
            'email' => 'viewer@mfmrd.gov.ki',
            'password' => bcrypt('password')
        ]);
        $licenseViewer->assignRole($licViewerRole);

        // License View All User
        $licenseViewAllUser = User::create([
            'name' => 'License View All User',
            'email' => 'viewall@mfmrd.gov.ki',
            'password' => bcrypt('password')
        ]);
        $licenseViewAllUser->assignRole($licViewAllRole);
    }
}