<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('feature_segments', function (Blueprint $table) {
            $table->id();
            $table->string('feature');
            $table->string('scope');
            $table->json('values');
            $table->boolean('active');
            $table->timestamps();

            $table->unique(['feature', 'scope', 'active']);
        });
    }
};
