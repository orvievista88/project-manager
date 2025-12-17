<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

$adminRole = Role::create(['name' => 'admin']);
$userRole  = Role::create(['name' => 'user']);

User::create([
    'name' => 'Admin',
    'email' => 'admin@test.com',
    'password' => Hash::make('password'),
    'role_id' => $adminRole->id,
]);

User::create([
    'name' => 'User',
    'email' => 'user@test.com',
    'password' => Hash::make('password'),
    'role_id' => $userRole->id,
]);

