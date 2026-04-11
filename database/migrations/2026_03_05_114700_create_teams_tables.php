<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->boolean('is_suspended')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('team_users')) {
            Schema::create('team_users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email');
                $table->string('role')->default('member');
                $table->string('status')->default('pending');
                $table->string('invitation_token')->unique()->nullable();
                $table->timestamp('invited_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();

                $table->unique(['team_id', 'email']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('team_users');
        Schema::dropIfExists('teams');
    }
};
