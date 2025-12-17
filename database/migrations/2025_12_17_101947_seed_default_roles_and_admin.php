<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Check if roles already exist
        if (!DB::table('roles')->where('name', 'Admin')->exists()) {
            $adminRoleId = DB::table('roles')->insertGetId([
                'name' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        }

        if (!DB::table('roles')->where('name', 'User')->exists()) {
            DB::table('roles')->insert([
                'name' => 'User',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Check if admin user exists
        if (!DB::table('users')->where('email', 'admin@example.com')->exists()) {
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
                'role_id' => $adminRoleId
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@example.com')->delete();
        DB::table('roles')->whereIn('name', ['Admin', 'User'])->delete();
    }
};
