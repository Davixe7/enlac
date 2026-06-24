<?php

use App\Models\Candidate;
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
        Schema::create('sponsorships', function(Blueprint $table){
            $table->id();
            $table->foreignId('candidate_id')->constrained()->onDelete('cascade');
            $table->foreignId('sponsor_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['parent', 'sponsor'])->default('parent');
            $table->decimal('amount', 10, 2);
            $table->string('amount_usd')->nullable();
            $table->decimal('frequency', 4,2);
            $table->unsignedTinyInteger('month_payday');
            $table->enum('address_type', ['home', 'office']);
            $table->boolean('wants_pickup')->default(false);
            $table->boolean('wants_reminder')->default(false);
            $table->boolean('wants_deductible_receipt')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
    }
};
