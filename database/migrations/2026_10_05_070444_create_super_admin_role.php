<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Every other role is created and named by admins on the Roles page.
     */
    public function up(): void
    {
        Role::findOrCreate('super_admin', 'web');
    }

    public function down(): void
    {
        Role::query()->where('name', 'super_admin')->where('guard_name', 'web')->delete();
    }
};
