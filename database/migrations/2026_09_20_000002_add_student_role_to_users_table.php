<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'faculty', 'student') NOT NULL DEFAULT 'student'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'student')->update(['role' => 'faculty']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin', 'faculty') NOT NULL DEFAULT 'faculty'");
        }
    }
};
