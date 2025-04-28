<?php

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SupportTicket::class);
            $table->foreignIdFor(User::class );
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('note');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replies');
    }
};
