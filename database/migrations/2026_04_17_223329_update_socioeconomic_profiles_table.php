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
        Schema::table('socioeconomic_profiles', function (Blueprint $table) {
            $table->string('requester_name')->nullable()->change();
            $table->string('requester_relationship')->nullable()->change();
            $table->string('requester_gender')->nullable()->change();
            $table->string('requester_marital_status')->nullable()->change();
            $table->string('requester_phone')->nullable()->change();
            $table->string('requester_origin')->nullable()->change();

            $table->string('address_street')->nullable()->change();
            $table->string('address_ext_num')->nullable()->change();
            $table->string('address_colony')->nullable()->change();
            $table->string('address_zip_code')->nullable()->change();
            $table->string('address_country')->nullable()->change();
            $table->string('address_state')->nullable()->change();
            $table->string('address_city')->nullable()->change();
            $table->string('transport_method')->nullable()->change();

            // Integers / Booleans
            $table->integer('requester_age')->nullable()->change();
            $table->integer('bathroom_count')->nullable()->change();
            $table->integer('bedroom_count')->nullable()->change();
            $table->integer('household_members_count')->nullable()->change();
            $table->boolean('other_disabled_members')->nullable()->change();
            $table->boolean('receives_govt_support')->nullable()->change();

            // Dates
            $table->date('requester_birth_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('socioeconomic_profiles', function (Blueprint $table) {
            //
        });
    }
};
