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
        Schema::table('diet_ingredients', function (Blueprint $table) {
            $table->renameColumn('masterdata_id', 'master_data_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diet_ingredients', function (Blueprint $table) {
            //
        });
    }
};
