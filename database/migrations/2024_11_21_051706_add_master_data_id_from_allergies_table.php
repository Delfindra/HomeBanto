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
        Schema::table('allergies', function (Blueprint $table) {
            $table->dropColumn('allergy_name');
            $table->foreignId('masterdata_id')->constrained('master_data')->cascadeOnDelete();
            $table->dropConstrainedForeignId('users_id');
           // $table->dropColumn('users_id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allergies', function (Blueprint $table) {
            //
        });
    }
};
