<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parent_quota_updates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('amount');
            $table->date('valid_since');
            $table->boolean('applied')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_quota_updates');
    }
};
