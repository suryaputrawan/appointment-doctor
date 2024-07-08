<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'menu appointment',
            'create appointment',
            'update appointment',
            'view appointment',
            'arrived appointment',
            'cancel appointment',
            'reschedule appointment',
            'menu specialities',
            'create specialities',
            'update specialities',
            'delete specialities',
            'menu services',
            'create services',
            'update services',
            'delete services',
            'menu doctors',
            'create doctors',
            'update doctors',
            'delete doctors',
            'menu doctor schedules',
            'create doctor schedules',
            'update doctor schedules',
            'delete doctor schedules',
            'menu hospitals',
            'create hospitals',
            'update hospitals',
            'delete hospitals',
            'menu off duty',
            'create off duty',
            'update off duty',
            'delete off duty',
            'menu sick letter',
            'create sick letter',
            'update sick letter',
            'delete sick letter',
            'print sick letter',
            'menu setting',
            'create parameter',
            'update parameter',
            'menu users',
            'create users',
            'update users',
            'menu role & permission',
        ];

        foreach ($permissions as $data) {
            Permission::create(['name' => $data]);
        }

        $role = Role::find(1);

        $role->givePermissionTo(['menu role & permission']);
    }
}
