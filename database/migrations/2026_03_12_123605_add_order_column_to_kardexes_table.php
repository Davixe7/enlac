<?php

use App\Models\Kardex;
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
        Schema::table('kardexes', function (Blueprint $table) {
            $table->unsignedTinyInteger('order');
        });

        $collection = Kardex::all();
        $collection = $collection->groupBy('category');

        foreach ($collection as $col) {
            $index = 1;
            foreach ($col as $item) {
                $item->update(['order' => $index++]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kardexes', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
